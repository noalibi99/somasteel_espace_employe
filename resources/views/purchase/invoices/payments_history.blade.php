@extends('layouts.app')

@section('title', "Historique des Paiements Fournisseurs")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Historique des Paiements Fournisseurs</h1>
        <a href="{{ route('purchase.invoices.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Compta
        </a>
    </div>

    @include('layouts.partials.flash_messages')

    {{-- Section des Filtres --}}
    <div class="bg-white shadow sm:rounded-lg p-4 md:p-6">
        <form method="GET" action="{{ route('purchase.payments.history') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 md:gap-4 items-end">
            <div class="xl:col-span-2">
                <label for="search_term" class="block text-xs font-medium text-gray-700">Recherche</label>
                <input type="text" name="search_term" id="search_term" value="{{ $request->input('search_term') }}" placeholder="N° Facture/BDC, Réf. Paiement, Fournisseur..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-700">Statut Paiement</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Tous</option>
                    @foreach($statuses as $statusCode => $statusLabel)
                        <option value="{{ $statusCode }}" {{ $request->input('status') == $statusCode ? 'selected' : '' }}>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="supplier_id" class="block text-xs font-medium text-gray-700">Fournisseur</label>
                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Tous</option>
                    @foreach($suppliers as $id => $name)
                        <option value="{{ $id }}" {{ $request->input('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="payment_method" class="block text-xs font-medium text-gray-700">Méthode Paiement</label>
                <select name="payment_method" id="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Toutes</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" {{ $request->input('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-700">Date Paiement (De)</label>
                <input type="date" name="date_from" id="date_from" value="{{ $request->input('date_from') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-700">Date Paiement (À)</label>
                <input type="date" name="date_to" id="date_to" value="{{ $request->input('date_to') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div class="flex items-end space-x-2 pt-5 md:pt-0">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-filter mr-2"></i> Filtrer
                </button>
                <a href="{{ route('purchase.payments.history') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i> Effacer
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-t border-gray-200">
            @if($paidInvoices->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucun paiement ne correspond à vos critères de recherche.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="paymentsHistoryTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Facture</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Paiement</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Fact.</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Payé</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Méthode</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf. Paiement</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Docs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($paidInvoices as $invoice)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('purchase.invoices.show', $invoice) }}" class="text-somasteel-orange hover:underline">{{ $invoice->invoice_number }}</a>
                                        <span class="block text-xs text-gray-500">BDC: <a href="{{route('purchase.orders.show', $invoice->purchaseOrder)}}" class="hover:underline">#{{$invoice->purchaseOrder->po_number}}</a></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $invoice->supplier->company_name ?? $invoice->purchaseOrder->supplier->company_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 text-right">{{ number_format($invoice->total_amount, 2, ',', ' ') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 font-semibold text-right">{{ number_format($invoice->amount_paid, 2, ',', ' ') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $invoice->payment_method }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="{{$invoice->payment_reference}}">{{ Str::limit($invoice->payment_reference, 20) ?? '-' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center space-x-1">
                                        @if($invoice->document_path)
                                        <a href="{{ Storage::url($invoice->document_path) }}" target="_blank" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-blue-600 hover:bg-blue-100" title="Voir Scan Facture">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        @endif
                                        @if($invoice->payment_document_path)
                                        <a href="{{ Storage::url($invoice->payment_document_path) }}" target="_blank" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-green-600 hover:bg-green-100" title="Voir Preuve Paiement">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($paidInvoices->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $paidInvoices->links('pagination::tailwind') }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script de recherche simple (identique aux autres pages index)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const table = document.getElementById('paymentsHistoryTable');
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    }
</script>
@endpush
