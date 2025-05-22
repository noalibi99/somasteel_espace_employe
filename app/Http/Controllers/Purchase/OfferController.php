<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\RFQ;
use App\Models\Supplier;
use App\Models\Line as PurchaseRequestLine; // Alias pour clarté
use App\Models\OfferLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(RFQ $rfq)
    {
        Gate::authorize('create', [Offer::class, $rfq]);

        $rfqSuppliersIds = $rfq->suppliers->pluck('id');
        $suppliersWithOfferIds = $rfq->offers->pluck('supplier_id');
        $availableSuppliers = Supplier::whereIn('id', $rfqSuppliersIds)
                                    ->whereNotIn('id', $suppliersWithOfferIds)
                                    ->orderBy('company_name')
                                    ->get();

        if ($availableSuppliers->isEmpty() && $rfq->suppliers->isNotEmpty()) {
             return redirect()->route('purchase.rfqs.show', $rfq)->with('info', 'Tous les fournisseurs contactés ont déjà soumis une offre pour ce RFQ.');
        }
        if ($rfq->suppliers->isEmpty()){
            return redirect()->route('purchase.rfqs.show', $rfq)->with('warning', 'Aucun fournisseur n\'est associé à ce RFQ. Veuillez d\'abord en ajouter.');
        }

        // Récupérer les lignes de la demande d'achat originale pour pré-remplir le formulaire d'offre
        $purchaseRequestLines = $rfq->purchaseRequest->lines()->with('article')->get();

        return view('purchase.offers.create', compact('rfq', 'availableSuppliers', 'purchaseRequestLines'));
    }

    public function store(Request $request, RFQ $rfq)
    {
        Gate::authorize('create', [Offer::class, $rfq]);

        $validated = $request->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where(function ($query) use ($rfq) {
                    $query->whereIn('id', $rfq->suppliers->pluck('id'));
                }),
                Rule::unique('offers')->where(function ($query) use ($rfq) {
                    return $query->where('rfq_id', $rfq->id);
                })
            ],
            'terms' => 'nullable|string|max:2000',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'offer_notes' => 'nullable|string|max:5000', // Renommé pour éviter confusion avec notes de ligne
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:5120',

            // Validation pour les lignes d'offre
            'offer_lines' => 'required|array|min:1',
            'offer_lines.*.purchase_request_line_id' => 'required|exists:lines,id', // 'lines' est la table des PR lines
            'offer_lines.*.article_id' => 'nullable|exists:articles,id',
            'offer_lines.*.description' => 'nullable|string|max:1000',
            'offer_lines.*.quantity_offered' => 'required|integer|min:0', // Peut être 0 si l'article n'est pas offert
            'offer_lines.*.unit_price' => 'required|numeric|min:0',
            'offer_lines.*.line_notes' => 'nullable|string|max:1000',
        ], [
            'supplier_id.unique' => 'Une offre de ce fournisseur existe déjà pour ce RFQ.',
            'offer_lines.required' => 'Au moins une ligne d\'article doit être présente dans l\'offre.',
            'offer_lines.*.purchase_request_line_id.required' => 'Chaque ligne d\'offre doit correspondre à une ligne de la demande d\'achat.',
            'offer_lines.*.quantity_offered.required' => 'La quantité offerte est requise pour chaque ligne.',
            'offer_lines.*.unit_price.required' => 'Le prix unitaire est requis pour chaque ligne.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('rfq_offers_attachments', 'public');
        }

        try {
            DB::beginTransaction();

            $offer = $rfq->offers()->create([
                'supplier_id' => $validated['supplier_id'],
                'terms' => $validated['terms'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['offer_notes'], // Utilisation de 'offer_notes'
                'attachment_path' => $attachmentPath,
            ]);

            foreach ($validated['offer_lines'] as $lineData) {
                // Récupérer la quantité demandée originale pour référence
                $originalPrLine = PurchaseRequestLine::find($lineData['purchase_request_line_id']);
                $quantityRequested = $originalPrLine ? $originalPrLine->quantity : 0;

                $offer->offerLines()->create([
                    'purchase_request_line_id' => $lineData['purchase_request_line_id'],
                    'article_id' => $lineData['article_id'] ?? $originalPrLine->article_id, // Fallback sur l'article original
                    'description' => $lineData['description'] ?? $originalPrLine->article->designation, // Fallback
                    'quantity_requested' => $quantityRequested,
                    'quantity_offered' => $lineData['quantity_offered'],
                    'unit_price' => $lineData['unit_price'],
                    // total_price sera calculé par le mutateur dans OfferLine
                    'notes' => $lineData['line_notes'] ?? null,
                ]);
            }

            if ($rfq->status == RFQ::STATUS_SENT || $rfq->status == RFQ::STATUS_DRAFT) {
                $rfq->status = RFQ::STATUS_RECEIVING_OFFERS;
                $rfq->save();
            }

            DB::commit();

            return redirect()->route('purchase.rfqs.show', $rfq)->with('success', 'Offre du fournisseur ajoutée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            // Log::error("Erreur ajout offre: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
            return back()->withInput()->with('error', 'Erreur lors de l\'ajout de l\'offre: ' . $e->getMessage());
        }
    }

    public function edit(RFQ $rfq, Offer $offer)
    {
        Gate::authorize('update', $offer);
        $offer->load('supplier', 'offerLines.purchaseRequestLine.article', 'offerLines.article');

        // Récupérer les lignes de la demande d'achat originale pour référence
        $purchaseRequestLines = $rfq->purchaseRequest->lines()->with('article')->get();

        return view('purchase.offers.edit', compact('rfq', 'offer', 'purchaseRequestLines'));
    }

    public function update(Request $request, RFQ $rfq, Offer $offer)
    {
        Gate::authorize('update', $offer);

        $validated = $request->validate([
            'terms' => 'nullable|string|max:2000',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'offer_notes' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:5120',

            'offer_lines' => 'required|array|min:1',
            'offer_lines.*.id' => 'nullable|exists:offer_lines,id', // Pour les lignes existantes
            'offer_lines.*.purchase_request_line_id' => 'required|exists:lines,id',
            'offer_lines.*.article_id' => 'nullable|exists:articles,id',
            'offer_lines.*.description' => 'nullable|string|max:1000',
            'offer_lines.*.quantity_offered' => 'required|integer|min:0',
            'offer_lines.*.unit_price' => 'required|numeric|min:0',
            'offer_lines.*.line_notes' => 'nullable|string|max:1000',
        ]);

        $currentAttachmentPath = $offer->attachment_path;
        $newAttachmentPath = $currentAttachmentPath;

        if ($request->hasFile('attachment')) {
            if ($currentAttachmentPath && Storage::disk('public')->exists($currentAttachmentPath)) {
                Storage::disk('public')->delete($currentAttachmentPath);
            }
            $newAttachmentPath = $request->file('attachment')->store('rfq_offers_attachments', 'public');
        }

        try {
            DB::beginTransaction();

            $offer->update([
                'terms' => $validated['terms'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['offer_notes'],
                'attachment_path' => $newAttachmentPath,
            ]);

            $existingLineIds = [];
            foreach ($validated['offer_lines'] as $lineData) {
                $originalPrLine = PurchaseRequestLine::find($lineData['purchase_request_line_id']);
                $quantityRequested = $originalPrLine ? $originalPrLine->quantity : 0;

                $offerLineData = [
                    'purchase_request_line_id' => $lineData['purchase_request_line_id'],
                    'article_id' => $lineData['article_id'] ?? $originalPrLine->article_id,
                    'description' => $lineData['description'] ?? $originalPrLine->article->designation,
                    'quantity_requested' => $quantityRequested,
                    'quantity_offered' => $lineData['quantity_offered'],
                    'unit_price' => $lineData['unit_price'],
                    'notes' => $lineData['line_notes'] ?? null,
                ];

                if (!empty($lineData['id'])) { // Ligne existante à mettre à jour
                    $offerLine = OfferLine::where('offer_id', $offer->id)->find($lineData['id']);
                    if ($offerLine) {
                        $offerLine->update($offerLineData);
                        $existingLineIds[] = $offerLine->id;
                    }
                } else { // Nouvelle ligne à créer
                    $newLine = $offer->offerLines()->create($offerLineData);
                    $existingLineIds[] = $newLine->id;
                }
            }

            // Supprimer les lignes qui n'ont pas été soumises dans le formulaire de modification
            $offer->offerLines()->whereNotIn('id', $existingLineIds)->delete();

            DB::commit();

            return redirect()->route('purchase.rfqs.show', $rfq)->with('success', 'Offre mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->hasFile('attachment') && $newAttachmentPath !== $currentAttachmentPath && Storage::disk('public')->exists($newAttachmentPath)) {
                 Storage::disk('public')->delete($newAttachmentPath);
            }
            // Log::error("Erreur MAJ offre: " . $e->getMessage() . " File: " . $e->getFile() . " Line: " . $e->getLine());
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour de l\'offre: ' . $e->getMessage());
        }
    }

    // La méthode destroy() doit aussi supprimer les offerLines associées (géré par onDelete('cascade'))
    public function destroy(RFQ $rfq, Offer $offer)
    {
        Gate::authorize('delete', $offer);

        $attachmentPath = $offer->attachment_path;

        try {
            DB::beginTransaction(); // Encapsuler dans une transaction

            // Les OfferLines seront supprimées par la contrainte onDelete('cascade')
            $offer->delete();

            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }

            if (!$rfq->offers()->exists() && $rfq->status == RFQ::STATUS_RECEIVING_OFFERS) {
                $rfq->status = RFQ::STATUS_SENT;
                $rfq->save();
            } elseif (!$rfq->offers()->exists() && $rfq->status == RFQ::STATUS_PROCESSING_OFFERS) {
                // S'il n'y a plus d'offres, on pourrait revenir à SENT ou RECEIVING_OFFERS
                $rfq->status = RFQ::STATUS_RECEIVING_OFFERS;
                $rfq->save();
            }


            DB::commit();
            return redirect()->route('purchase.rfqs.show', $rfq)->with('success', 'Offre supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de l\'offre: ' . $e->getMessage());
        }
    }
}
