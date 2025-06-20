@extends('layouts.app')

@section('title', "Détail Facture #{$invoice->invoice_number}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Détails Facture Fournisseur <span class="text-somasteel-orange">#{{ $invoice->invoice_number }}</span></h1>
        <div class="flex flex-wrap gap-2">
            @can('update', $invoice)
                @if(!in_array($invoice->status, [App\Models\Invoice::STATUS_PAID, App\Models\Invoice::STATUS_CANCELLED]))
                <a href="{{ route('purchase.invoices.edit', $invoice) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
                @endif
            @endcan
            @if($invoice->document_path)
                <a href="{{ Storage::url($invoice->document_path) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" target="_blank">
                    <i class="fas fa-file-pdf mr-2"></i> Voir Scan Facture
                </a>
            @endif
            <a href="{{ route('purchase.invoices.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fas fa-list-ul mr-2"></i> Historique Factures
            </a>
        </div>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 space-y-6"> {{-- Section principale d'informations --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                     <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de la Facture</h3>
                        </div>
                        <span class="mt-2 sm:mt-0 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $invoice->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$invoice->status_color.'-100 text-'.$invoice->status_color.'-800' }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">N° Facture</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold sm:mt-0 sm:col-span-2">{{ $invoice->invoice_number }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Fournisseur</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <a href="{{route('purchase.suppliers.show', $invoice->supplier_id)}}" class="text-somasteel-orange hover:underline">
                                    {{ $invoice->supplier->company_name ?? $invoice->purchaseOrder->supplier->company_name }}
                                </a>
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Bon de Commande</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <a href="{{ route('purchase.orders.show', $invoice->purchaseOrder) }}" class="text-somasteel-orange hover:underline">#{{ $invoice->purchaseOrder->po_number }}</a>
                            </dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Date Facture</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->invoice_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Date Échéance</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</dd>
                        </div>
                         <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Montant HT</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($invoice->amount_ht, 2, ',', ' ') }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Montant TVA</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($invoice->vat_amount, 2, ',', ' ') }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Montant TTC</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold sm:mt-0 sm:col-span-2">{{ number_format($invoice->total_amount, 2, ',', ' ') }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Montant Payé</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($invoice->amount_paid, 2, ',', ' ') }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-semibold text-gray-700">Montant Dû</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold sm:mt-0 sm:col-span-2">{{ number_format($invoice->amount_due, 2, ',', ' ') }}</dd>
                        </div>
                        @if($invoice->notes)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Notes Facture</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $invoice->notes }}</dd>
                        </div>
                        @endif
                        @if($invoice->validatedBy)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Validée/Rejetée par</dt>
                            <dd class="mt-1 text-sm text-gray-500 italic sm:mt-0 sm:col-span-2">{{ $invoice->validatedBy->nom }} {{ $invoice->validatedBy->prénom }} le {{ $invoice->validated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                         @endif
                    </dl>
                </div>
            </div>

            @if($invoice->payment_date || $invoice->payment_document_path)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Informations du Dernier Paiement Enregistré</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        @if($invoice->payment_date)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Date Paiement</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->payment_date->format('d/m/Y H:i') }}</dd>
                        </div>
                        @endif
                        @if($invoice->payment_method)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Méthode</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->payment_method }}</dd>
                        </div>
                        @endif
                        @if($invoice->payment_reference)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Référence</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $invoice->payment_reference }}</dd>
                        </div>
                        @endif
                        @if($invoice->payment_document_path)
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Justificatif de Paiement</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <a href="{{ Storage::url($invoice->payment_document_path) }}" target="_blank" class="text-somasteel-orange hover:underline flex items-center">
                                    <i class="fas fa-file-alt mr-1.5"></i> Voir Document
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-5 space-y-6"> {{-- Colonne d'actions et d'infos liées --}}
            @if($invoice->status === App\Models\Invoice::STATUS_PENDING_VALIDATION)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Validation de la Facture</h3>
                    <p class="mt-1 text-sm text-gray-500 mb-4">Veuillez vérifier la conformité de cette facture avec le BDC et les livraisons avant d'approuver.</p>
                    <div class="space-y-3 sm:space-y-0 sm:flex sm:space-x-3">
                        <form action="{{ route('purchase.invoices.validateAction', $invoice) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" name="action" value="approve" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check-circle mr-2"></i> Approuver la Facture
                            </button>
                        </form>
                        <button type="button" onclick="openRejectModal()" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-times-circle mr-2"></i> Rejeter la Facture
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array($invoice->status, [App\Models\Invoice::STATUS_VALIDATED, App\Models\Invoice::STATUS_PARTIALLY_PAID]) && !$invoice->is_fully_paid)
            <div class="bg-white shadow sm:rounded-lg">
                 <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Enregistrement du Paiement</h3>
                     <p class="mt-1 text-sm text-gray-500 mb-4">Cette facture est validée et attend un paiement.</p>
                    <a href="{{ route('purchase.invoices.recordPaymentForm', $invoice) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                        <i class="fas fa-dollar-sign mr-2"></i> Enregistrer un Paiement
                    </a>
                </div>
            </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Livraisons Associées au BDC</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($invoice->purchaseOrder->deliveries as $delivery)
                        <li class="hover:bg-gray-50 transition-colors">
                            <a href="{{ route('purchase.deliveries.show', $delivery) }}" class="block px-4 py-3 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        BL Fournisseur: <span class="text-somasteel-orange">{{ $delivery->delivery_reference }}</span>
                                    </p>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $delivery->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$delivery->status_color.'-100 text-'.$delivery->status_color.'-800' }}">{{ $delivery->status_label }}</span>
                                </div>
                                <p class="text-xs text-gray-500">Date: {{ $delivery->delivery_date->format('d/m/Y') }}</p>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-sm text-gray-500">Aucune livraison enregistrée pour ce BDC.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal de Rejet (identique à celui dans purchase_requests/show) -->
    @if($invoice->status === App\Models\Invoice::STATUS_PENDING_VALIDATION)
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out opacity-0">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 transform transition-all duration-300 ease-in-out scale-95 opacity-0" id="rejectModalContent">
            <form action="{{ route('purchase.invoices.validateAction', $invoice) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Motif du Rejet de la Facture</h3>
                    <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Veuillez indiquer le motif du rejet <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required minlength="10"
                              class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('rejection_reason', 'rejectionForm') border-red-500 @enderror">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason', 'rejectionForm') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror {{-- Nommer le bag d'erreur si besoin --}}
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">Annuler</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Confirmer le Rejet</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($invoice->status === App\Models\Invoice::STATUS_PENDING_VALIDATION)
<script>
    // Script pour la modale de rejet (identique à celui de purchase_requests/show)
    const rejectModal = document.getElementById('rejectModal');
    const rejectModalContent = document.getElementById('rejectModalContent');

    function openRejectModal() {
        if (rejectModal) {
            rejectModal.classList.remove('hidden');
            setTimeout(() => {
                rejectModal.classList.remove('opacity-0');
                if (rejectModalContent) {
                    rejectModalContent.classList.remove('scale-95', 'opacity-0');
                    rejectModalContent.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
        }
    }

    function closeRejectModal() {
         if (rejectModal && rejectModalContent) {
            rejectModalContent.classList.remove('scale-100', 'opacity-100');
            rejectModalContent.classList.add('scale-95', 'opacity-0');
            rejectModal.classList.add('opacity-0');
            setTimeout(() => {
                rejectModal.classList.add('hidden');
            }, 300);
        }
    }
    if (rejectModal) {
        rejectModal.addEventListener('click', function(event) {
            if (event.target === rejectModal) closeRejectModal();
        });
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape" && rejectModal && !rejectModal.classList.contains('hidden')) {
            closeRejectModal();
        }
    });

    @if ($errors->hasBag('rejectionForm') || $errors->has('rejection_reason')) // Si erreur de validation pour la modale, la réouvrir
        openRejectModal();
    @endif
</script>
@endif
@endpush
