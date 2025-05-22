<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RfqSentToSupplierNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Offer;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Notifications\OfferSelectedWarehouseNotification;


class RFQController extends Controller
{
    public function __construct()
    {
        // Protéger toutes les méthodes par authentification
        $this->middleware('auth');
    }

    /**
     * Display a dashboard for the purchasing department.
     * Lists approved purchase requests awaiting Rfq creation.
     */
    public function dashboard()
    {
        Gate::authorize('view-purchase-dashboard'); // Utilise la Gate définie dans AuthServiceProvider

        $approvedRequests = PurchaseRequest::where('status', PurchaseRequest::STATUS_APPROVED)
            ->with('user', 'lines.article')
            ->latest()
            ->paginate(10);

        return view('purchase.rfqs.dashboard_achat', compact('approvedRequests'));
    }

    /**
     * Show the form for creating a new Rfq from a PurchaseRequest.
     */
    public function create(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('create', [Rfq::class, $purchaseRequest]);

        // Vérifier si un Rfq existe déjà pour cette demande pour éviter les doublons
        if ($purchaseRequest->rfq) {
            return redirect()->route('purchase.rfqs.show', $purchaseRequest->rfq)
                ->with('warning', 'Un Rfq existe déjà pour cette demande d\'achat.');
        }

        $suppliers = Supplier::orderBy('company_name')->get();
        return view('purchase.rfqs.create', compact('purchaseRequest', 'suppliers'));
    }

    /**
     * Store a newly created Rfq in storage.
     */
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('create', [Rfq::class, $purchaseRequest]);

        if ($purchaseRequest->rfq) {
            return redirect()->route('purchase.rfqs.show', $purchaseRequest->rfq)
                ->with('warning', 'Un Rfq existe déjà pour cette demande d\'achat.');
        }

        $validationRules = [
            'suppliers_rfq' => 'required|array|min:1', // Fournisseurs pour le Rfq
            'suppliers_rfq.*' => 'exists:suppliers,id',
            'notes' => 'nullable|string|max:2000',
            'deadline_for_offers' => 'nullable|date|after_or_equal:today',
            'send_emails_action' => 'required|in:0,1', // 0 = brouillon, 1 = envoyer emails
            'suppliers_to_email' => 'nullable|array', // Fournisseurs à notifier, présent si send_emails_action = 1
            'suppliers_to_email.*' => 'exists:suppliers,id',
        ];

        // Validation conditionnelle pour les champs email
        if ($request->input('send_emails_action') == '1') {
            // Si on envoie des emails, il faut qu'au moins un fournisseur soit coché pour l'email
            $validationRules['suppliers_to_email'] = 'required|array|min:1';
            $validationRules['email_subject'] = 'required|string|max:255';
            $validationRules['email_body'] = 'required|string|max:5000';
        }

        $validated = $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Le statut du Rfq dépend si on envoie les emails ou non
            $rfqStatus = ($validated['send_emails_action'] == '1' && !empty($validated['suppliers_to_email']))
                         ? Rfq::STATUS_SENT
                         : Rfq::STATUS_DRAFT;

            $rfq = $purchaseRequest->rfq()->create([
                'status' => $rfqStatus,
                'notes' => $validated['notes'],
                'deadline_for_offers' => $validated['deadline_for_offers'],
            ]);

            // Lier les fournisseurs sélectionnés pour le Rfq
            $rfq->suppliers()->attach($validated['suppliers_rfq']);

            $purchaseRequest->status = PurchaseRequest::STATUS_RFQ_IN_PROGRESS;
            $purchaseRequest->save();

            DB::commit(); // Commit avant l'envoi d'emails

            $message = 'Rfq #' . $rfq->id . ' créé avec succès. Statut : ' . $rfq->status_label . '.';

            // Envoyer les notifications si l'action est d'envoyer et qu'il y a des fournisseurs à notifier
            if ($validated['send_emails_action'] == '1' && !empty($validated['suppliers_to_email'])) {
                $suppliersToNotify = Supplier::findMany($validated['suppliers_to_email']);
                $emailSubject = $validated['email_subject'];
                $emailBody = $validated['email_body'];
                $emailsSentCount = 0;

                foreach ($suppliersToNotify as $supplier) {
                    // S'assurer que le fournisseur est aussi dans la liste des fournisseurs du Rfq
                    // (sécurité, même si l'UI devrait le gérer)
                    if (in_array($supplier->id, $validated['suppliers_rfq']) && $supplier->contact_email) {
                        try {
                            Notification::send($supplier, new RfqSentToSupplierNotification($rfq, $supplier, $emailSubject, $emailBody));
                            $emailsSentCount++;
                        } catch (\Exception $e) {
                            Log::error("Erreur envoi email Rfq au fournisseur ID {$supplier->id}: " . $e->getMessage());
                        }
                    } elseif (!$supplier->contact_email) {
                         Log::warning("Le fournisseur ID {$supplier->id} ({$supplier->company_name}) n'a pas d'email de contact pour la notification Rfq.");
                    }
                }
                $message .= " {$emailsSentCount} email(s) de notification envoyé(s).";
            } elseif ($validated['send_emails_action'] == '1' && empty($validated['suppliers_to_email'])) {
                $message .= " Aucune notification par email envoyée car aucun fournisseur n'a été coché pour être notifié.";
            }


            return redirect()->route('purchase.rfqs.show', $rfq)
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création Rfq: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du Rfq. ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the Rfqs.
     */
    public function index()
    {
        Gate::authorize('viewAny', Rfq::class);

        $rfqs = Rfq::with('purchaseRequest.user') // Eager load pour performance
            ->latest()
            ->paginate(10);

        return view('purchase.rfqs.index', compact('rfqs'));
    }

    /**
     * Display the specified Rfq.
     */
    // public function show(Rfq $rfq)
    // {
    //     Gate::authorize('view', $rfq);

    //     $rfq->load('purchaseRequest.user', 'purchaseRequest.lines.article', 'suppliers', 'offers.supplier');
    //     // 'offers.supplier' sera utile pour la phase suivante

    //     return view('purchase.rfqs.show', compact('rfq'));
    // }
    public function show(RFQ $rfq)
    {
        Gate::authorize('view', $rfq);

        $rfq->load(
            'purchaseRequest.user',
            'purchaseRequest.lines.article',
            'suppliers',
            'offers.supplier', // Charger les offres et leur fournisseur
            'offers.offerLines.article',
            'offers.offerLines.purchaseRequestLine.article',
            'selectedOffer.supplier',
            'selectedOffer.offerLines.article',
            'selectedOffer.offerLines.purchaseRequestLine.article'
        );

        $purchaseRequestLines = $rfq->purchaseRequest->lines;

        // Identifier l'offre la moins chère globalement
        $cheapestOverallOfferId = null;
        if ($rfq->offers->isNotEmpty()) {
            $cheapestOverallOffer = $rfq->offers->sortBy(function ($offer) {
                return $offer->total_offer_price; // Utilise l'accesseur
            })->first();
            if ($cheapestOverallOffer) {
                $cheapestOverallOfferId = $cheapestOverallOffer->id;
            }
        }

        // Identifier les offres les moins chères par ligne d'article (plus complexe)
        $cheapestOfferPerLine = [];
        foreach ($purchaseRequestLines as $prLine) {
            $minPriceForLine = null;
            $supplierIdsWithMinPrice = [];

            foreach ($rfq->offers as $offer) {
                $offerLine = $offer->offerLines->firstWhere('purchase_request_line_id', $prLine->id);
                if ($offerLine && $offerLine->quantity_offered > 0) {
                    // On pourrait comparer le prix total de la ligne (PU * Qté) ou juste le PU
                    // Pour la comparaison, le prix unitaire est souvent plus pertinent si les quantités offertes varient
                    $currentPrice = $offerLine->unit_price; // ou $offerLine->total_price si vous préférez

                    if (is_null($minPriceForLine) || $currentPrice < $minPriceForLine) {
                        $minPriceForLine = $currentPrice;
                        $supplierIdsWithMinPrice = [$offer->supplier_id];
                    } elseif ($currentPrice == $minPriceForLine) {
                        $supplierIdsWithMinPrice[] = $offer->supplier_id;
                    }
                }
            }
            if (!is_null($minPriceForLine)) {
                $cheapestOfferPerLine[$prLine->id] = [
                    'min_price' => $minPriceForLine,
                    'supplier_ids' => $supplierIdsWithMinPrice,
                ];
            }
        }

        return view('purchase.rfqs.show', compact('rfq', 'purchaseRequestLines', 'cheapestOverallOfferId', 'cheapestOfferPerLine'));
    }

    /**
     * Show the form for editing the specified Rfq.
     * (Potentiellement pour changer les fournisseurs, notes, deadline si brouillon)
     */
    public function edit(Rfq $rfq)
    {
        Gate::authorize('update', $rfq);

        if ($rfq->status !== Rfq::STATUS_DRAFT) {
            return redirect()->route('purchase.rfqs.show', $rfq)->with('warning', 'Seuls les Rfq en brouillon peuvent être modifiés.');
        }

        $suppliers = Supplier::orderBy('company_name')->get();
        $purchaseRequest = $rfq->purchaseRequest; // Pour réutiliser la vue de création si similaire
        $rfq->load('suppliers'); // Charger les fournisseurs déjà sélectionnés

        return view('purchase.rfqs.edit', compact('rfq', 'purchaseRequest', 'suppliers'));
    }

    /**
     * Update the specified Rfq in storage.
     */
    public function update(Request $request, Rfq $rfq)
    {
        Gate::authorize('update', $rfq);

        if ($rfq->status !== Rfq::STATUS_DRAFT) {
            return redirect()->route('purchase.rfqs.show', $rfq)->with('warning', 'Seuls les Rfq en brouillon peuvent être modifiés.');
        }

        $validated = $request->validate([
            'suppliers' => 'required|array|min:1',
            'suppliers.*' => 'exists:suppliers,id',
            'notes' => 'nullable|string|max:2000',
            'deadline_for_offers' => 'nullable|date|after_or_equal:today',
             // 'status' => 'sometimes|in:'.Rfq::STATUS_DRAFT.','.Rfq::STATUS_SENT // Si on permet de changer le statut ici
        ]);

        try {
            DB::beginTransaction();

            $rfq->update([
                'notes' => $validated['notes'],
                'deadline_for_offers' => $validated['deadline_for_offers'],
                // 'status' => $validated['status'] ?? $rfq->status,
            ]);

            $rfq->suppliers()->sync($validated['suppliers']); // sync gère ajout/suppression

            DB::commit();

            // if ($rfq->status === Rfq::STATUS_SENT) {
            //     // TODO: Notifier les fournisseurs si le statut passe à "Envoyé"
            // }

            return redirect()->route('purchase.rfqs.show', $rfq)
                ->with('success', 'Rfq mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour du Rfq: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Rfq from storage.
     * (Uniquement si brouillon)
     */
    public function destroy(Rfq $rfq)
    {
        Gate::authorize('delete', $rfq);

        if ($rfq->status !== Rfq::STATUS_DRAFT) {
            return redirect()->route('purchase.rfqs.show', $rfq)->with('warning', 'Seuls les Rfq en brouillon peuvent être supprimés.');
        }

        try {
            DB::beginTransaction();
            // Rétablir le statut de la PurchaseRequest à 'approved'
            $purchaseRequest = $rfq->purchaseRequest;
            $purchaseRequest->status = PurchaseRequest::STATUS_APPROVED;
            $purchaseRequest->save();

            $rfq->delete(); // Ceci devrait aussi supprimer les entrées dans rfq_supplier grâce à onDelete('cascade')

            DB::commit();
            return redirect()->route('purchase.rfq.dashboard')->with('success', 'Rfq supprimé et demande d\'achat rétablie.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression du Rfq: ' . $e->getMessage());
        }
    }

    public function selectOffer(Request $request, Rfq $rfq)
    {
        Gate::authorize('selectOffer', [Offer::class, $rfq]); // Utilise la policy Offer, méthode selectOffer

        // $request->validate([
        //     'selected_offer_id' => [
        //         'required',
        //         Rule::exists('offers', 'id')->where(function ($query) use ($rfq) { // <-- ICI
        //             $query->where('rfq_id', $rfq->id);
        //         }),
        //     ],
        // ]);

        // $offerToSelect = Offer::find($request->input('selected_offer_id'));

        // if (!$offerToSelect) {
        //     return redirect()->route('purchase.rfqs.show', $rfq)->with('error', 'Offre non trouvée.');
        // }

        $offerToSelect = Offer::with('supplier', 'offerLines.article')->findOrFail($request->input('selected_offer_id'));

        try {
            DB::beginTransaction();
            $rfq->selected_offer_id = $offerToSelect->id;
            $rfq->status = RFQ::STATUS_SELECTION_DONE;
            $rfq->save();
            DB::commit();

            // Notifier le magasin et le service achat
            $warehouseUsers = User::where('type', 'magasinier')->orWhere('type', 'administrateur')->get();
            $purchasingUsers = User::where('type', 'purchase')->get(); // Le service achat aussi
            $usersToNotify = $warehouseUsers->merge($purchasingUsers)->unique('id');

            if ($usersToNotify->isNotEmpty()) {
                Notification::send($usersToNotify, new OfferSelectedWarehouseNotification($rfq, $offerToSelect));
            }

            return redirect()->route('purchase.rfqs.show', $rfq)->with('success', 'Offre du fournisseur ' . $offerToSelect->supplier->company_name . ' sélectionnée.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase.rfqs.show', $rfq)->with('error', 'Erreur lors de la sélection de l\'offre: ' . $e->getMessage());
        }
    }

    /**
     * Deselect the currently selected offer for an Rfq.
     */
    public function deselectOffer(Rfq $rfq)
    {
        // Autorisation similaire à selectOffer ou une nouvelle méthode de policy
        Gate::authorize('selectOffer', [Offer::class, $rfq]); // Réutilisation de la policy pour simplifier

        if (!$rfq->selected_offer_id) {
            return redirect()->route('purchase.rfqs.show', $rfq)->with('info', 'Aucune offre n\'est actuellement sélectionnée.');
        }

        try {
            DB::beginTransaction();

            // Optionnel: Mettre à jour le drapeau sur l'ancien modèle Offer si utilisé
            // if ($rfq->selectedOffer) {
            //     $rfq->selectedOffer->update(['is_selected' => false]);
            // }

            $rfq->selected_offer_id = null;
            // Revenir à un statut approprié, par exemple PROCESSING_OFFERS
            // ou si on veut pouvoir re-comparer. Si on a des offres, on reste en processing.
            $rfq->status = $rfq->offers()->exists() ? Rfq::STATUS_PROCESSING_OFFERS : Rfq::STATUS_RECEIVING_OFFERS;
            $rfq->save();

            DB::commit();

            return redirect()->route('purchase.rfqs.show', $rfq)->with('success', 'Sélection de l\'offre annulée. Vous pouvez choisir une autre offre.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase.rfqs.show', $rfq)->with('error', 'Erreur lors de l\'annulation de la sélection: ' . $e->getMessage());
        }
    }
}
