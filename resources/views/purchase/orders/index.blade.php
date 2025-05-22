@extends('layouts.app')

@section('title', "Liste des Bons de Commande")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Liste des Bons de Commande (BDC)</h1>
        {{-- Le bouton de création est sur la page RFQ Show après sélection d'une offre --}}
    </div>

    @include('layouts.partials.flash_messages')

    <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher par N° BDC, fournisseur, statut..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-t border-gray-200">
            @if($purchaseOrders->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucun bon de commande trouvé.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="purchaseOrdersTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° BDC</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Cmd.</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé par</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchaseOrders as $po)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $po->supplier->company_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->order_date ? $po->order_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium text-right">{{ number_format($po->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $po->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$po->status_color.'-100 text-'.$po->status_color.'-800' }} {{ $po->status_color == 'teal' ? 'bg-teal-100 text-teal-800' : '' }} {{ $po->status_color == 'purple' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $po->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->user->nom ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-1">
                                        <a href="{{ route('purchase.orders.show', $po) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-somasteel-orange hover:bg-somasteel-orange/10" title="Voir"><i class="fas fa-eye"></i></a>
                                        @can('update', $po)
                                            @if(in_array($po->status, [App\Models\PurchaseOrder::STATUS_DRAFT, App\Models\PurchaseOrder::STATUS_PENDING_APPROVAL]))
                                            <a href="{{ route('purchase.orders.edit', $po) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-yellow-600 hover:bg-yellow-100" title="Modifier"><i class="fas fa-edit"></i></a>
                                            @endif
                                        @endcan
                                        <a href="{{ route('purchase.orders.pdf', $po) }}" target="_blank" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-red-600 hover:bg-red-100" title="Télécharger PDF"><i class="fas fa-file-pdf"></i></a>
                                         @can('delete', $po)
                                            @if($po->status === App\Models\PurchaseOrder::STATUS_DRAFT)
                                            <form action="{{ route('purchase.orders.destroy', $po) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce BDC en brouillon ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-gray-500 hover:bg-gray-100 hover:text-red-600" title="Supprimer Brouillon"><i class="fas fa-trash"></i></button>
                                            </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($purchaseOrders->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $purchaseOrders->links('pagination::tailwind') }}
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
        const rows = document.querySelectorAll('#purchaseOrdersTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
