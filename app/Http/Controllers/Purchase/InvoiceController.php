<?php

namespace App\Http\Controllers\Purchase;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\PurchaseProcessCompletedNotification;
use Illuminate\Support\Facades\Notification;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        Gate::authorize('viewAccountingDashboard', Invoice::class);

        $pendingValidationInvoices = Invoice::where('status', Invoice::STATUS_PENDING_VALIDATION)
            ->with('purchaseOrder.supplier')
            ->latest('invoice_date')
            ->get();

        $pendingPaymentInvoices = Invoice::where('status', Invoice::STATUS_VALIDATED)
            ->orWhere('status', Invoice::STATUS_PARTIALLY_PAID)
            ->with('purchaseOrder.supplier')
            ->orderBy('due_date', 'asc')
            ->get();


        $purchaseOrdersToInvoice = PurchaseOrder::whereIn('status', [
                PurchaseOrder::STATUS_PARTIALLY_DELIVERED,
                PurchaseOrder::STATUS_FULLY_DELIVERED,
            ])

            ->with('supplier', 'invoices')
            ->orderBy('order_date', 'desc')
            ->paginate(10, ['*'], 'pos_page');


        return view('purchase.invoices.dashboard_compta', compact(
            'pendingValidationInvoices',
            'pendingPaymentInvoices',
            'purchaseOrdersToInvoice'
        ));
    }

    public function index()
    {
        Gate::authorize('viewAny', Invoice::class);
        $invoices = Invoice::with('purchaseOrder.supplier', 'validatedBy')
            ->latest('invoice_date')
            ->paginate(15);
        return view('purchase.invoices.index', compact('invoices'));
    }

    public function create(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create', [Invoice::class, $purchaseOrder]);
        $purchaseOrder->load('supplier', 'purchaseOrderLines.article', 'deliveries.deliveryLines');

        return view('purchase.invoices.create', compact('purchaseOrder'));
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create', [Invoice::class, $purchaseOrder]);

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,NULL,id,supplier_id,'.$purchaseOrder->supplier_id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'amount_ht' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'invoice_notes' => 'nullable|string|max:2000',
            'document' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:5120', // 5MB
        ], [
            'invoice_number.unique' => 'Ce numéro de facture existe déjà pour ce fournisseur.',
            'total_amount.required' => 'Le montant TTC est requis.',
        ]);

        // Vérification de cohérence
        if (abs(($validated['amount_ht'] + $validated['vat_amount']) - $validated['total_amount']) > 0.01) {
            return back()->withInput()->with('error', 'Le montant TTC ne correspond pas à HT + TVA.');
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('supplier_invoices', 'public');
        }

        try {
            DB::beginTransaction();

            $invoice = $purchaseOrder->invoices()->create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'amount_ht' => $validated['amount_ht'],
                'vat_amount' => $validated['vat_amount'],
                'total_amount' => $validated['total_amount'],
                'status' => Invoice::STATUS_PENDING_VALIDATION,
                'notes' => $validated['invoice_notes'],
                'document_path' => $documentPath,
            ]);

            DB::commit();
            return redirect()->route('purchase.invoices.show', $invoice)
                ->with('success', 'Facture #' . $invoice->invoice_number . ' enregistrée et en attente de validation.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($documentPath && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            return back()->withInput()->with('error', 'Erreur enregistrement facture: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);
        $invoice->load('purchaseOrder.supplier', 'purchaseOrder.deliveries.deliveryLines', 'validatedBy');
        return view('purchase.invoices.show', compact('invoice'));
    }


    public function edit(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);
        $invoice->load('purchaseOrder');
        return view('purchase.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        $validated = $request->validate([
            'invoice_number' => [
                'required','string','max:255',
                Rule::unique('invoices', 'invoice_number')
                    ->ignore($invoice->id)
                    ->where('supplier_id', $invoice->supplier_id)
            ],
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'amount_ht' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'invoice_notes' => 'nullable|string|max:2000',
            'document' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:5120',
        ]);

        if (abs(($validated['amount_ht'] + $validated['vat_amount']) - $validated['total_amount']) > 0.01) {
            return back()->withInput()->with('error', 'Le montant TTC ne correspond pas à HT + TVA.');
        }

        $currentDocumentPath = $invoice->document_path;
        $newDocumentPath = $currentDocumentPath;

        if ($request->hasFile('document')) {
            if ($currentDocumentPath && Storage::disk('public')->exists($currentDocumentPath)) {
                Storage::disk('public')->delete($currentDocumentPath);
            }
            $newDocumentPath = $request->file('document')->store('supplier_invoices', 'public');
        }

        try {
            $invoice->update([
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'amount_ht' => $validated['amount_ht'],
                'vat_amount' => $validated['vat_amount'],
                'total_amount' => $validated['total_amount'],
                'notes' => $validated['invoice_notes'],
                'document_path' => $newDocumentPath,
            ]);
            return redirect()->route('purchase.invoices.show', $invoice)->with('success', 'Facture mise à jour.');
        } catch (\Exception $e) {
            if ($request->hasFile('document') && $newDocumentPath !== $currentDocumentPath && Storage::disk('public')->exists($newDocumentPath)) {
                 Storage::disk('public')->delete($newDocumentPath);
            }
            return back()->withInput()->with('error', 'Erreur MAJ facture: ' . $e->getMessage());
        }
    }

    public function validateInvoiceAction(Request $request, Invoice $invoice)
    {
        Gate::authorize('validateInvoice', $invoice);

        if ($invoice->status !== Invoice::STATUS_PENDING_VALIDATION) {
            return redirect()->route('purchase.invoices.show', $invoice)->with('error', 'Cette facture ne peut plus être validée/rejetée (statut actuel: '.$invoice->status_label.').');
        }

        $action = $request->input('action'); // 'approve' ou 'reject'
        $rejectionReason = $request->input('rejection_reason');

        if ($action === 'approve') {
            $invoice->status = Invoice::STATUS_VALIDATED;
            $invoice->validated_by_id = Auth::id();
            $invoice->validated_at = now();
            $invoice->save();
            // TODO: Notifier le service achat ou le demandeur que la facture est validée
            return redirect()->route('purchase.invoices.show', $invoice)->with('success', 'Facture validée avec succès.');
        } elseif ($action === 'reject') {
            if (empty($rejectionReason)) {
                return back()->withInput()->withErrors(['rejection_reason' => 'Le motif de rejet est requis.']);
            }
            $invoice->status = Invoice::STATUS_REJECTED;
            $invoice->notes = ($invoice->notes ? $invoice->notes . "\n" : '') . "Rejetée le " . now()->format('d/m/Y') . " par " . Auth::user()->nom . ": " . $rejectionReason;
            $invoice->validated_by_id = Auth::id(); // Personne qui a rejeté
            $invoice->validated_at = now(); // Date du rejet
            $invoice->save();
            // TODO: Notifier le service achat/fournisseur du rejet
            return redirect()->route('purchase.invoices.show', $invoice)->with('warning', 'Facture rejetée.');
        }
        return redirect()->route('purchase.invoices.show', $invoice)->with('error', 'Action non reconnue.');
    }


    public function recordPaymentForm(Invoice $invoice)
    {
        Gate::authorize('recordPayment', $invoice);
        if (!in_array($invoice->status, [Invoice::STATUS_VALIDATED, Invoice::STATUS_PARTIALLY_PAID])) {
             return redirect()->route('purchase.invoices.show', $invoice)->with('error', 'Impossible d\'enregistrer un paiement pour cette facture (statut: '.$invoice->status_label.').');
        }
        return view('purchase.invoices.record_payment', compact('invoice'));
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        Gate::authorize('recordPayment', $invoice);

        if (!in_array($invoice->status, [Invoice::STATUS_VALIDATED, Invoice::STATUS_PARTIALLY_PAID])) {
            return redirect()->route('purchase.invoices.show', $invoice)->with('error', 'Impossible d\'enregistrer un paiement pour cette facture (statut: '.$invoice->status_label.').');
        }

        $validated = $request->validate([
            'amount_paid_now' => 'required|numeric|min:0.01|max:'.$invoice->amount_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000',
            'payment_document' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:5120',
        ]);

        $paymentDocumentPath = $invoice->payment_document_path;
        if ($request->hasFile('payment_document')) {
            if ($paymentDocumentPath && Storage::disk('public')->exists($paymentDocumentPath)) {
                Storage::disk('public')->delete($paymentDocumentPath);
            }
            $paymentDocumentPath = $request->file('payment_document')->store('payment_proofs', 'public');
        }

        try {
            DB::beginTransaction();

            $invoice->amount_paid = ($invoice->amount_paid ?? 0) + $validated['amount_paid_now'];
            $invoice->payment_date = $validated['payment_date'];
            $invoice->payment_method = $validated['payment_method'];
            $invoice->payment_reference = $validated['payment_reference'];
            $invoice->payment_document_path = $paymentDocumentPath;
            $invoice->notes = ($invoice->notes ? $invoice->notes . "\n" : '') .
                              "Paiement enregistré (" . number_format($validated['amount_paid_now'], 2, ',', ' ') . ") le " .
                              now()->format('d/m/Y') . ". Notes: " . ($validated['payment_notes'] ?? 'N/A');

            $invoice->save();

            if ($invoice->is_fully_paid) {
                $purchaseOrder = $invoice->purchaseOrder;
                // Charger les relations nécessaires pour la logique et la notification
                $purchaseOrder->loadMissing(['supplier', 'invoices', 'rfq.purchaseRequest.user', 'rfq.purchaseRequest.validator']);

                $allInvoicesForPoPaid = true;
                if ($purchaseOrder->invoices->isNotEmpty()) {
                    foreach ($purchaseOrder->invoices as $poInvoice) {
                        if (!$poInvoice->is_fully_paid && $poInvoice->status !== Invoice::STATUS_CANCELLED) {
                            $allInvoicesForPoPaid = false;
                            break;
                        }
                    }
                } else {
                    $allInvoicesForPoPaid = false;
                }

                if ($allInvoicesForPoPaid && $purchaseOrder->status !== PurchaseOrder::STATUS_COMPLETED) {
                    $purchaseOrder->status = PurchaseOrder::STATUS_COMPLETED;
                    $purchaseOrder->save();

                    $purchaseRequest = $purchaseOrder->rfq->purchaseRequest;
                    if ($purchaseRequest->status !== PurchaseRequest::STATUS_PROCESSED) {
                        $purchaseRequest->status = PurchaseRequest::STATUS_PROCESSED;
                        $purchaseRequest->save();
                    }

                    $usersToNotify = collect();
                    $purchasingUsers = User::where('type', 'purchase')->orWhere('type', 'administrateur')->get();
                    $usersToNotify = $usersToNotify->merge($purchasingUsers);

                    if ($purchaseOrder->rfq && $purchaseOrder->rfq->purchaseRequest && $purchaseOrder->rfq->purchaseRequest->user) {
                        $originalRequester = $purchaseOrder->rfq->purchaseRequest->user;
                        $usersToNotify = $usersToNotify->push($originalRequester);
                    }
                    // Optionnel: Notifier aussi le directeur qui avait validé la PR
                    // if ($purchaseOrder->rfq && $purchaseOrder->rfq->purchaseRequest && $purchaseOrder->rfq->purchaseRequest->validator) {
                    //    $director = $purchaseOrder->rfq->purchaseRequest->validator;
                    //    $usersToNotify = $usersToNotify->push($director);
                    // }
                    $usersToNotify = $usersToNotify->unique('id');

                    if ($usersToNotify->isNotEmpty()) {

                        Notification::send($usersToNotify, new PurchaseProcessCompletedNotification($purchaseOrder, $invoice));
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchase.invoices.show', $invoice)->with('success', 'Paiement enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
             if ($request->hasFile('payment_document') && $paymentDocumentPath !== $invoice->payment_document_path && Storage::disk('public')->exists($paymentDocumentPath)) {
                 Storage::disk('public')->delete($paymentDocumentPath);
            }
            // Log::error('Erreur enregistrement paiement: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
        }
    }


    public function paymentsHistory(Request $request)
    {
        // Gate::authorize('viewAny', Invoice::class); // Ou une permission spécifique pour voir l'historique des paiements
        if (!Gate::allows('viewAny', Invoice::class)) {
            abort(403, "Vous n'êtes pas autorisé à consulter cet historique.");
        }


        $query = Invoice::query()
            ->whereNotNull('payment_date') // Uniquement les factures avec une date de paiement
            ->where(function($q) { // Uniquement les factures payées ou partiellement payées
                $q->where('status', Invoice::STATUS_PAID)
                  ->orWhere('status', Invoice::STATUS_PARTIALLY_PAID);
            })
            ->with(['purchaseOrder.supplier', 'validatedBy', 'purchaseOrder.user' /* Créateur du PO */, 'purchaseOrder.rfq.purchaseRequest.user' /* Demandeur initial */])
            ->latest('payment_date'); // Trier par date de paiement la plus récente

        // Filtres
        if ($request->filled('search_term')) {
            $searchTerm = $request->input('search_term');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                  ->orWhere('payment_reference', 'like', "%{$searchTerm}%")
                  ->orWhereHas('purchaseOrder', function ($poq) use ($searchTerm) {
                      $poq->where('po_number', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('supplier', function ($sq) use ($searchTerm) {
                      $sq->where('company_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status')); // Peut filtrer entre 'paid' et 'partially_paid'
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->input('date_to'));
        }

        $paidInvoices = $query->paginate(20)->appends($request->all());

        // Données pour les filtres (on peut réutiliser ou affiner)
        $statuses = [
            Invoice::STATUS_PAID => (new Invoice(['status' => Invoice::STATUS_PAID]))->status_label,
            Invoice::STATUS_PARTIALLY_PAID => (new Invoice(['status' => Invoice::STATUS_PARTIALLY_PAID]))->status_label,
        ];
        $suppliers = Supplier::orderBy('company_name')->pluck('company_name', 'id');
        $paymentMethods = Invoice::whereNotNull('payment_method')
                                 ->select('payment_method')
                                 ->distinct()
                                 ->pluck('payment_method')
                                 ->filter()
                                 ->sort();


        return view('purchase.invoices.payments_history', compact('paidInvoices', 'statuses', 'suppliers', 'paymentMethods', 'request'));
    }



    // Destroy est généralement pour annuler une facture ou la supprimer si elle est en brouillon/erreur.
    // Pour l'instant, on ne l'implémente pas complètement.
}
