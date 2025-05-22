@extends('layouts.app')

@section('title', "Dashboard Comptabilité - Factures")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord Comptabilité</h1>
        <a href="{{ route('purchase.invoices.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-history mr-2"></i> Historique des Factures
        </a>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Colonne pour les factures en attente --}}
        <div class="space-y-6">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-100 border-b border-yellow-200 rounded-t-lg">
                    <h3 class="text-lg leading-6 font-medium text-yellow-800">
                        <i class="fas fa-hourglass-half mr-2"></i> Factures en Attente de Validation
                    </h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($pendingValidationInvoices as $invoice)
                        <li class="hover:bg-gray-50 transition-colors">
                            <a href="{{ route('purchase.invoices.show', $invoice) }}" class="block px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-somasteel-orange truncate">
                                        Facture #{{ $invoice->invoice_number }}
                                        <span class="text-gray-600 font-normal">({{ $invoice->purchaseOrder->supplier->company_name }})</span>
                                    </p>
                                    <p class="text-sm text-gray-700 font-semibold">
                                        {{ number_format($invoice->total_amount, 2, ',', ' ') }} {{-- devise --}}
                                    </p>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <p>Date facture: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                                    <p>BDC: <span class="font-medium">#{{$invoice->purchaseOrder->po_number}}</span></p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-sm text-gray-500 text-center">Aucune facture en attente de validation.</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-blue-100 border-b border-blue-200 rounded-t-lg">
                    <h3 class="text-lg leading-6 font-medium text-blue-800">
                        <i class="fas fa-credit-card mr-2"></i> Factures Validées en Attente de Paiement
                    </h3>
                </div>
                 <ul class="divide-y divide-gray-200">
                    @forelse($pendingPaymentInvoices as $invoice)
                        <li class="hover:bg-gray-50 transition-colors">
                            <a href="{{ route('purchase.invoices.show', $invoice) }}" class="block px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-somasteel-orange truncate">
                                        Facture #{{ $invoice->invoice_number }}
                                        <span class="text-gray-600 font-normal">({{ $invoice->purchaseOrder->supplier->company_name }})</span>
                                    </p>
                                    <p class="text-sm text-gray-700 font-semibold">
                                        {{ number_format($invoice->amount_due, 2, ',', ' ') }} {{-- devise --}} à payer
                                    </p>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                    <p>Échéance: {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</p>
                                    @if($invoice->status === App\Models\Invoice::STATUS_PARTIALLY_PAID)
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Partiellement Payée</span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-sm text-gray-500 text-center">Aucune facture en attente de paiement.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Colonne pour les BDC à facturer --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-receipt mr-2"></i> Bons de Commande Livrés (Potentiellement à Facturer)
                </h3>
            </div>
            <div class="border-t border-gray-200">
                @if($purchaseOrdersToInvoice->isEmpty())
                    <p class="px-6 py-10 text-center text-sm text-gray-500">Aucun BDC livré nécessitant une action de facturation immédiate.</p>
                @else
                <ul class="divide-y divide-gray-200">
                    @foreach($purchaseOrdersToInvoice as $po)
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('purchase.orders.show', $po) }}" class="text-sm font-medium text-somasteel-orange hover:underline truncate">BDC #{{ $po->po_number }}</a>
                                    <p class="text-xs text-gray-500">{{ $po->supplier->company_name }} - <span class="px-1 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full {{ $po->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$po->status_color.'-100 text-'.$po->status_color.'-800' }}">{{ $po->status_label }}</span></p>
                                </div>
                                @can('create', [App\Models\Invoice::class, $po])
                                    <a href="{{ route('purchase.invoices.create', $po) }}" class="ml-4 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                                        <i class="fas fa-plus-circle mr-1.5"></i> Créer Facture
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endforeach
                </ul>
                @endif
            </div>
             @if($purchaseOrdersToInvoice->hasPages())
            <div class="p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                {{ $purchaseOrdersToInvoice->appends(['pos_page' => $purchaseOrdersToInvoice->currentPage()])->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
