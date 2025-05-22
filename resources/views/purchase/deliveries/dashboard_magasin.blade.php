@extends('layouts.app')

@section('title', "Dashboard Magasin - Réceptions")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord Magasin - Réceptions</h1>
        <a href="{{ route('purchase.deliveries.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-history mr-2"></i> Historique des Réceptions
        </a>
    </div>
    <p class="text-sm text-gray-600">Liste des Bons de Commande en attente de livraison ou partiellement livrés.</p>

    @include('layouts.partials.flash_messages')

    <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher par N° BDC, Fournisseur..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Bons de Commande à Réceptionner
            </h3>
        </div>
        <div class="border-t border-gray-200">
            @if($pendingDeliveryPOs->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucun bon de commande en attente de réception pour le moment.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="pendingPOsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° BDC</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Cmd.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Liv. Attendue</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut BDC</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pendingDeliveryPOs as $po)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $po->supplier->company_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->order_date ? $po->order_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->expected_delivery_date_global ? $po->expected_delivery_date_global->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $po->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$po->status_color.'-100 text-'.$po->status_color.'-800' }} {{ $po->status_color == 'teal' ? 'bg-teal-100 text-teal-800' : '' }} {{ $po->status_color == 'purple' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $po->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @can('create', [App\Models\Delivery::class, $po])
                                            <a href="{{ route('purchase.deliveries.create', $po) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                                                <i class="fas fa-truck-loading mr-1.5"></i> Enregistrer Réception
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Non autorisé</span>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($pendingDeliveryPOs->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $pendingDeliveryPOs->links('pagination::tailwind') }}
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
        const rows = document.querySelectorAll('#pendingPOsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
