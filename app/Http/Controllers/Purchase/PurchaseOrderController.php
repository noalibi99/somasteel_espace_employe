<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Rfq;
use App\Models\Offer; // Importer Offer
use App\Models\Supplier; // Pour le filtre fournisseur
use App\Models\User;
use App\Notifications\PurchaseOrderSentToStakeholders;
use Illuminate\Support\Facades\Notification;
use App\Models\PurchaseRequest; // Importer PurchaseRequest
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Pour la génération PDF


class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('viewAny', PurchaseOrder::class);
        $purchaseOrders = PurchaseOrder::with('supplier', 'user', 'rfq.purchaseRequest.user')
            ->latest()
            ->paginate(15);
        return view('purchase.orders.index', compact('purchaseOrders'));
    }

    // Le formulaire de création est initié à partir d'un Rfq avec une offre sélectionnée
    public function create(Rfq $rfq)
    {
        Gate::authorize('create', [PurchaseOrder::class, $rfq]);

        if (!$rfq->selected_offer_id) {
            return redirect()->route('purchase.rfqs.show', $rfq)
                ->with('error', 'Aucune offre n\'a été sélectionnée pour ce Rfq.');
        }
        if (PurchaseOrder::where('rfq_id', $rfq->id)->where('offer_id', $rfq->selected_offer_id)->exists()) {
             return redirect()->route('purchase.rfqs.show', $rfq)
                ->with('info', 'Un Bon de Commande existe déjà pour cette offre sélectionnée.');
        }


        $selectedOffer = $rfq->selectedOffer()->with('supplier', 'offerLines.article', 'offerLines.purchaseRequestLine.article')->firstOrFail();

        // Préparer les données par défaut pour le PO
        $defaultShippingAddress = "Adresse de livraison Somasteel..."; // À configurer
        $defaultBillingAddress = "Adresse de facturation Somasteel..."; // À configurer

        return view('purchase.orders.create', compact('rfq', 'selectedOffer', 'defaultShippingAddress', 'defaultBillingAddress'));
    }

    public function store(Request $request, Rfq $rfq)
    {
        Gate::authorize('create', [PurchaseOrder::class, $rfq]);

        if (!$rfq->selected_offer_id) {
            return redirect()->route('purchase.rfqs.show', $rfq)->with('error', 'Aucune offre sélectionnée.');
        }
        $selectedOffer = Offer::with('offerLines')->findOrFail($rfq->selected_offer_id);

        $validated = $request->validate([
            'order_date' => 'required|date',
            'expected_delivery_date_global' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'required|string|max:1000',
            'billing_address' => 'required|string|max:1000',
            'payment_terms' => 'nullable|string|max:255',
            'po_notes' => 'nullable|string|max:5000',
            // Valider les lignes si on permet de les modifier à ce stade (pour l'instant on copie de l'offre)
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::create([
                'rfq_id' => $rfq->id,
                'offer_id' => $selectedOffer->id,
                'supplier_id' => $selectedOffer->supplier_id,
                'user_id' => Auth::id(),
                'status' => PurchaseOrder::STATUS_DRAFT, // Commence en brouillon
                'order_date' => $validated['order_date'],
                'expected_delivery_date_global' => $validated['expected_delivery_date_global'],
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['po_notes'],
            ]);

            // Copier les lignes de l'offre sélectionnée vers les lignes du PO
            foreach ($selectedOffer->offerLines as $offerLine) {
                if ($offerLine->quantity_offered > 0) { // Seulement si une quantité est offerte
                    $purchaseOrder->purchaseOrderLines()->create([
                        'offer_line_id' => $offerLine->id,
                        'article_id' => $offerLine->article_id,
                        'description' => $offerLine->description ?? $offerLine->article->designation,
                        'quantity_ordered' => $offerLine->quantity_offered,
                        'unit_price' => $offerLine->unit_price,
                        // 'total_price' sera calculé par le mutateur
                        // 'expected_delivery_date' => // Peut être repris de l'offre ou défini ici
                        'notes' => $offerLine->notes,
                    ]);
                }
            }

            // Mettre à jour le statut du Rfq
            $rfq->status = Rfq::STATUS_ORDER_CREATED;
            $rfq->save();

            // Mettre à jour le statut de la PurchaseRequest originale (optionnel, ou un statut "ordered")
            $purchaseRequest = $rfq->purchaseRequest;
            if ($purchaseRequest->status !== PurchaseRequest::STATUS_PROCESSED) { // Éviter d'écraser un statut final
                 $purchaseRequest->status = PurchaseRequest::STATUS_ORDERED; // Assurez-vous que ce statut existe
                 $purchaseRequest->save();
            }


            DB::commit();
            // dd($purchaseOrder->id, $purchaseOrder->exists, $purchaseOrder->toArray());

            return redirect()->route('purchase.orders.show', $purchaseOrder)
                ->with('success', 'Bon de Commande #' . $purchaseOrder->po_number . ' créé avec succès en tant que brouillon.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erreur création PO: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return back()->withInput()->with('error', 'Erreur lors de la création du Bon de Commande: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('view', $purchaseOrder);
        // dd($purchaseOrder->exists, $purchaseOrder->toArray());
        $purchaseOrder->load(
            'supplier', 'user', 'rfq.purchaseRequest.user',
            'offer.supplier', // Pour avoir le fournisseur de l'offre originale
            'purchaseOrderLines.article',
            'purchaseOrderLines.offerLine.purchaseRequestLine.article' // Pour remonter à l'article original PR
        );
        // dd($purchaseOrder);
        return view('purchase.orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);
        $purchaseOrder->load('purchaseOrderLines.article');
        // Ici on pourrait recharger Rfq et Offer si on veut afficher des infos de contexte
        $rfq = $purchaseOrder->rfq()->with('selectedOffer.offerLines.article')->first();


        return view('purchase.orders.edit', compact('purchaseOrder', 'rfq'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('update', $purchaseOrder);

        $validated = $request->validate([
            'order_date' => 'required|date',
            'expected_delivery_date_global' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'required|string|max:1000',
            'billing_address' => 'required|string|max:1000',
            'payment_terms' => 'nullable|string|max:255',
            'po_notes' => 'nullable|string|max:5000',

            // Valider les lignes si on permet de les modifier
            'po_lines' => 'sometimes|array',
            'po_lines.*.id' => 'required_with:po_lines|exists:purchase_order_lines,id,purchase_order_id,'.$purchaseOrder->id,
            'po_lines.*.quantity_ordered' => 'required_with:po_lines|integer|min:0',
            'po_lines.*.unit_price' => 'required_with:po_lines|numeric|min:0',
            'po_lines.*.description' => 'required_with:po_lines|string|max:1000',
            'po_lines.*.expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'po_lines.*.notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder->update([
                'order_date' => $validated['order_date'],
                'expected_delivery_date_global' => $validated['expected_delivery_date_global'],
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['po_notes'],
            ]);

            if ($request->has('po_lines')) {
                foreach ($validated['po_lines'] as $lineId => $lineData) {
                    $poLine = PurchaseOrderLine::find($lineData['id']); // ou $purchaseOrder->purchaseOrderLines()->find($lineData['id']);
                    if ($poLine) {
                        $poLine->update([
                            'quantity_ordered' => $lineData['quantity_ordered'],
                            'unit_price' => $lineData['unit_price'],
                            'description' => $lineData['description'],
                            'expected_delivery_date' => $lineData['expected_delivery_date'],
                            'notes' => $lineData['notes'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchase.orders.show', $purchaseOrder)->with('success', 'Bon de Commande mis à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur MAJ PO: ' . $e->getMessage());
        }
    }


    public function destroy(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('delete', $purchaseOrder);
        // Logique pour annuler le PO, remettre Rfq et PR à un état antérieur si nécessaire.
        // Pour l'instant, simple suppression si en brouillon.
        // En pratique, on ne supprime pas un PO, on l'annule (change son statut).
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
            return redirect()->route('purchase.orders.show', $purchaseOrder)->with('error', 'Seuls les BDC en brouillon peuvent être supprimés. Envisagez de l\'annuler.');
        }

        try {
            DB::beginTransaction();
            // Revenir au statut précédent du Rfq
            $rfq = $purchaseOrder->rfq;
            if ($rfq) {
                $rfq->status = Rfq::STATUS_SELECTION_DONE;
                $rfq->save();
            }
            // Idem pour PurchaseRequest
            $pr = $rfq ? $rfq->purchaseRequest : null;
            if ($pr && $pr->status === PurchaseRequest::STATUS_ORDERED) {
                $pr->status = PurchaseRequest::STATUS_APPROVED; // Ou un autre statut approprié
                $pr->save();
            }

            $purchaseOrder->delete(); // Ceci supprimera aussi les lignes grâce à onDelete('cascade')
            DB::commit();
            return redirect()->route('purchase.orders.index')->with('success', 'Bon de Commande supprimé.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur suppression PO: ' . $e->getMessage());
        }
    }


    public function downloadPDF(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('view', $purchaseOrder);
        $purchaseOrder->load('supplier', 'purchaseOrderLines.article', 'user'); // Charger les relations nécessaires

        // Passer des données à la vue PDF
        $data = ['purchaseOrder' => $purchaseOrder];
        $pdf = Pdf::loadView('purchase.orders.pdf', $data);

        // Optionnel: Définir le nom du fichier
        $filename = 'BDC-' . $purchaseOrder->po_number . '.pdf';

        // Afficher dans le navigateur ou télécharger
        // return $pdf->stream($filename); // Pour afficher
        return $pdf->download($filename); // Pour télécharger
    }

    public function sendToSupplier(Request $requestHttp, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('sendToSupplier', $purchaseOrder);

        // Valider le formulaire si des infos supplémentaires sont demandées pour l'email
        // $validated = $request->validate([...]);

        if (!in_array($purchaseOrder->status, [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_APPROVED])) {
            return redirect()->route('purchase.orders.show', $purchaseOrder)->with('error', 'Ce BDC ne peut pas être envoyé (statut invalide).');
        }

        try {
            $purchaseOrder->load('supplier', 'rfq.purchaseRequest.user', 'purchaseOrderLines.article'); // Charger relations

        $purchaseOrder->status = PurchaseOrder::STATUS_SENT_TO_SUPPLIER;
        $purchaseOrder->sent_to_supplier_at = now();
        $purchaseOrder->save();

        // Notifier le magasinier
        $warehouseUsers = User::where('type', 'magasinier')->orWhere('type', 'administrateur')->get();
        // Notifier le demandeur original
        $originalRequester = $purchaseOrder->rfq->purchaseRequest->user;
        // Notifier le service achat aussi (pour info)
        $purchasingUsers = User::where('type', 'purchase')->get();

        $usersToNotify = $warehouseUsers->merge($purchasingUsers);
        if ($originalRequester) {
            $usersToNotify = $usersToNotify->push($originalRequester);
        }
        $usersToNotify = $usersToNotify->unique('id');


        if ($usersToNotify->isNotEmpty()) {
            Notification::send($usersToNotify, new PurchaseOrderSentToStakeholders($purchaseOrder));
        }

        // TODO: Idéalement, envoyer aussi le PDF au fournisseur réel.
        // $pdf = Pdf::loadView('purchase.orders.pdf', ['purchaseOrder' => $purchaseOrder]);
        // Mail::to($purchaseOrder->supplier->contact_email)
        //      ->send(new PurchaseOrderToSupplierMail($purchaseOrder, $pdf->output()));

        return redirect()->route('purchase.orders.show', $purchaseOrder)->with('success', 'Bon de Commande marqué comme envoyé au fournisseur et notifications envoyées.');

        } catch (\Exception $e) {
            return redirect()->route('purchase.orders.show', $purchaseOrder)->with('error', 'Erreur lors de l\'envoi au fournisseur: ' . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        Gate::authorize('viewHistory', PurchaseOrder::class);
        
        $query = PurchaseOrder::with([
            'supplier',
            'user', // Créateur du PO (Service Achat)
            'rfq.purchaseRequest.user', // Demandeur original
            'purchaseOrderLines',
            // 'deliveries', // Charger si vous voulez afficher le statut de livraison directement
            // 'invoices'    // Charger si vous voulez afficher le statut de facturation directement
        ])->latest('order_date'); // Trier par date de commande la plus récente

        $currentUser = Auth::user();

        // Filtre par rôle : L'ouvrier ne voit que les commandes issues de ses demandes d'achat
        if ($currentUser->isOuvrier() && !$currentUser->isAdmin() && !$currentUser->isDirector() && !$currentUser->isPurchase() && !$currentUser->isMagasinier() && !$currentUser->isComptable() && !$currentUser->isRH()) {
             $query->whereHas('rfq.purchaseRequest', function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            });
        }
        // Les autres rôles (admin, directeur, achat, magasin, compta) peuvent tout voir par défaut (pas de filtre supplémentaire ici)
        // Vous pourriez ajouter des filtres plus fins si un directeur ne voit que les POs de son département, etc.


        // Filtres appliqués par l'utilisateur
        if ($request->filled('search_term')) {
            $searchTerm = $request->input('search_term');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('po_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('supplier', function ($sq) use ($searchTerm) {
                      $sq->where('company_name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('rfq.purchaseRequest.user', function ($urq) use ($searchTerm) { // Recherche sur le demandeur original
                      $urq->where('nom', 'like', "%{$searchTerm}%")
                          ->orWhere('prénom', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('requester_id')) { // Filtre par demandeur original
            $query->whereHas('rfq.purchaseRequest', function ($q) use ($request) {
                $q->where('user_id', $request->input('requester_id'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->input('date_to'));
        }

        $purchaseOrders = $query->paginate(20)->appends($request->all());

        // Données pour les filtres
        $statuses = PurchaseOrder::select('status')->distinct()->pluck('status')->mapWithKeys(function ($status) {
            // Créer un objet PO temporaire juste pour utiliser l'accesseur status_label
            $tempPo = new PurchaseOrder(['status' => $status]);
            return [$status => $tempPo->status_label];
        })->sort();

        $suppliers = Supplier::orderBy('company_name')->pluck('company_name', 'id');
        // Pour le filtre demandeur, on prend les utilisateurs qui ont déjà fait des demandes ayant mené à des POs
        $requesters = User::whereHas('purchaseRequests.rfq.purchaseOrders')->orderBy('nom')->pluck('nom', 'id')->map(function($name, $id){
            $user = User::find($id); // Pour obtenir le prénom
            return $user->nom . ' ' . $user->prénom;
        });


        return view('purchase.orders.history', compact('purchaseOrders', 'statuses', 'suppliers', 'requesters', 'request'));
    }
}
