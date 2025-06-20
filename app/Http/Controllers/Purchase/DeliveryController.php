<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Notifications\ProductsReceivedNotification;
use App\Notifications\POReadyForInvoicingNotification;
use Illuminate\Support\Facades\Notification;


class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Dashboard pour les magasiniers: liste des PO en attente de livraison
    public function dashboard()
    {
        Gate::authorize('viewDeliveryDashboard', Delivery::class); // Policy de classe, pas d'instance

        $pendingDeliveryPOs = PurchaseOrder::whereIn('status', [
            PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
            PurchaseOrder::STATUS_ACKNOWLEDGED,
            PurchaseOrder::STATUS_PARTIALLY_DELIVERED,
        ])
        ->with('supplier', 'purchaseOrderLines') // Charger les lignes pour voir ce qui est attendu
        ->orderBy('expected_delivery_date_global', 'asc')
        ->orderBy('order_date', 'asc')
        ->paginate(15);

        return view('purchase.deliveries.dashboard_magasin', compact('pendingDeliveryPOs'));
    }

    public function index()
    {
        Gate::authorize('viewAny', Delivery::class);
        $deliveries = Delivery::with('purchaseOrder.supplier', 'receivedBy')
            ->latest()
            ->paginate(15);
        return view('purchase.deliveries.index', compact('deliveries'));
    }


    // Formulaire pour enregistrer une nouvelle livraison pour un PO spécifique
    public function create(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create', [Delivery::class, $purchaseOrder]);
        $purchaseOrder->load('supplier', 'purchaseOrderLines.article');

        // Pour chaque ligne de PO, calculer ce qui est encore attendu
        foreach ($purchaseOrder->purchaseOrderLines as $line) {
            // La quantité déjà reçue est sur la ligne de PO, mise à jour par les livraisons précédentes
            $line->quantity_still_expected = $line->quantity_ordered - $line->quantity_received;
        }

        return view('purchase.deliveries.create', compact('purchaseOrder'));
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create', [Delivery::class, $purchaseOrder]);

        $validated = $request->validate([
            'delivery_reference' => 'required|string|max:255', // BL Fournisseur
            'delivery_date' => 'required|date',
            'delivery_notes' => 'nullable|string|max:2000', // Notes sur la livraison globale

            'delivery_lines' => 'required|array|min:1',
            // La clé de 'delivery_lines' sera l'ID de la purchase_order_line
            'delivery_lines.*.quantity_received' => 'required|integer|min:0',
            'delivery_lines.*.line_notes' => 'nullable|string|max:1000',
            // 'delivery_lines.*.is_confirmed' => 'nullable|boolean', // On peut le forcer à true par défaut ici
        ]);

        try {
            DB::beginTransaction();

            $delivery = $purchaseOrder->deliveries()->create([
                'delivery_reference' => $validated['delivery_reference'],
                'delivery_date' => $validated['delivery_date'],
                'received_by_id' => Auth::id(),
                'status' => Delivery::STATUS_PENDING_CONFIRMATION, // Ou directement FULLY_RECEIVED si pas de workflow de confirmation
                'notes' => $validated['delivery_notes'],
            ]);

            $allLinesFullyReceivedForThisDelivery = true;
            $anyLineReceivedInThisDelivery = false;

            foreach ($validated['delivery_lines'] as $poLineId => $lineData) {
                $poLine = PurchaseOrderLine::findOrFail($poLineId);
                $quantityReceivedInForm = (int) $lineData['quantity_received'];

                if ($quantityReceivedInForm > 0) {
                    $anyLineReceivedInThisDelivery = true;
                    $delivery->deliveryLines()->create([
                        'purchase_order_line_id' => $poLine->id,
                        'article_id' => $poLine->article_id,
                        'quantity_received' => $quantityReceivedInForm,
                        'notes' => $lineData['line_notes'],
                        'is_confirmed' => true, // Confirmer par défaut lors de la saisie
                    ]);

                    // Mettre à jour la quantité reçue sur la ligne de PO
                    $poLine->quantity_received = ($poLine->quantity_received ?? 0) + $quantityReceivedInForm;
                    $poLine->save();

                    // Mettre à jour le statut de l'article si c'est un 'draft' et première réception
                    if ($poLine->article && $poLine->article->status === Article::STATUS_DRAFT) {
                        $poLine->article->status = Article::STATUS_APPROVED;
                        $poLine->article->save();
                    }
                }
                 // Vérifier si cette ligne de PO est maintenant totalement livrée
                if ($poLine->quantity_received < $poLine->quantity_ordered) {
                    $allLinesFullyReceivedForThisDelivery = false; // Faux si une ligne du PO n'est pas encore complète
                }
            }

            if (!$anyLineReceivedInThisDelivery) {
                DB::rollBack(); // Annuler si rien n'a été reçu
                return back()->withInput()->with('error', 'Aucune quantité n\'a été enregistrée comme reçue.');
            }


            $delivery->status = Delivery::STATUS_FULLY_RECEIVED;
            $delivery->save();


            // Mettre à jour le statut du PurchaseOrder
            $purchaseOrder->refresh(); // Recharger pour avoir les dernières quantités reçues sur les lignes
            $allPoLinesCompleted = true;
            foreach ($purchaseOrder->purchaseOrderLines as $po_line) {
                if ($po_line->quantity_received < $po_line->quantity_ordered) {
                    $allPoLinesCompleted = false;
                    break;
                }
            }

            if ($allPoLinesCompleted) {
                $purchaseOrder->status = PurchaseOrder::STATUS_FULLY_DELIVERED;
            } else {
                $purchaseOrder->status = PurchaseOrder::STATUS_PARTIALLY_DELIVERED;
            }
            $purchaseOrder->save();

            if ($purchaseOrder->status === PurchaseOrder::STATUS_FULLY_DELIVERED) {
                $accountingUsers = User::where('type', 'comptable')->get();
                if ($accountingUsers->isNotEmpty()) {
                    Notification::send($accountingUsers, new POReadyForInvoicingNotification($purchaseOrder));
                }
            }



            DB::commit();

            // Notifier Service Achat et Demandeur Original
            $purchasingUsers = User::where('type', 'purchase')->get();
            $originalRequester = $purchaseOrder->rfq->purchaseRequest->user;

            $usersToNotify = $purchasingUsers;
            if ($originalRequester) {
                $usersToNotify = $usersToNotify->push($originalRequester);
            }
            $usersToNotify = $usersToNotify->unique('id');

            if ($usersToNotify->isNotEmpty()) {
                Notification::send($usersToNotify, new ProductsReceivedNotification($delivery));
            }

            return redirect()->route('purchase.deliveries.show', $delivery)
                ->with('success', 'Réception enregistrée avec succès. BL: ' . $delivery->delivery_reference);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erreur enregistrement réception: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de la réception: ' . $e->getMessage());
        }
    }


    public function show(Delivery $delivery)
    {
        Gate::authorize('view', $delivery);
        $delivery->load('purchaseOrder.supplier', 'receivedBy', 'deliveryLines.article', 'deliveryLines.purchaseOrderLine.article');
        return view('purchase.deliveries.show', compact('delivery'));
    }
}
