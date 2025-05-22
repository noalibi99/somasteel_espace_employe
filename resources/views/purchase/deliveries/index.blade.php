@extends('layouts.app')

@section('title', "Historique des Réceptions")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Historique des Réceptions</h1>
        <a href="{{ route('purchase.deliveries.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-dolly-flatbed mr-2"></i> Dashboard Magasin
        </a>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher par ID, BL, BDC, Fournisseur..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-t border-gray-200">
             @if($deliveries->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucune réception enregistrée pour le moment.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="deliveriesTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Liv.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf. BL Fourn.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° BDC</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Liv.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reçu par</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut Liv.</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($deliveries as $delivery)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.deliveries.show', $delivery) }}">{{ $delivery->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $delivery->delivery_reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('purchase.orders.show', $delivery->purchaseOrder) }}" class="hover:underline">{{ $delivery->purchaseOrder->po_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $delivery->purchaseOrder->supplier->company_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->receivedBy->nom ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $delivery->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$delivery->status_color.'-100 text-'.$delivery->status_color.'-800' }}">
                                            {{ $delivery->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <a href="{{ route('purchase.deliveries.show', $delivery) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-somasteel-orange hover:bg-somasteel-orange/10" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                        {{-- @can('update', $delivery) ... @endcan --}}
                                        {{-- @can('delete', $delivery) ... @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($deliveries->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $deliveries->links('pagination::tailwind') }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#deliveriesTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
