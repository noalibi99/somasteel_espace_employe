@extends('layouts.app')

@section('title', "Liste des Demandes de Prix (RFQ)")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Liste des Demandes de Prix (RFQ)</h1>
        @can('view-purchase-dashboard')
        <a href="{{ route('purchase.rfq.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Achat
        </a>
        @endcan
    </div>

    @include('layouts.partials.flash_messages')

    <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher par ID, demandeur, statut..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-t border-gray-200">
            @if($rfqs->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucune Demande de Prix (RFQ) trouvée.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="rfqsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID RFQ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demande Achat</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur Initial</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Création RFQ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Limite Offres</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rfqs as $rfq)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.rfqs.show', $rfq) }}">#{{ $rfq->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($rfq->purchaseRequest)
                                        <a href="{{ route('purchase.requests.show', $rfq->purchaseRequest) }}" class="hover:underline">DA #{{ $rfq->purchaseRequest->id }}</a>
                                        @else N/A @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $rfq->purchaseRequest->user->nom ?? 'N/A' }} {{ $rfq->purchaseRequest->user->prénom ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $rfq->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $rfq->deadline_for_offers ? $rfq->deadline_for_offers->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $rfq->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$rfq->status_color.'-100 text-'.$rfq->status_color.'-800' }}">
                                            {{ $rfq->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <a href="{{ route('purchase.rfqs.show', $rfq) }}" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" title="Voir Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $rfq)
                                            @if(in_array($rfq->status, [App\Models\RFQ::STATUS_DRAFT, App\Models\RFQ::STATUS_SENT, App\Models\RFQ::STATUS_RECEIVING_OFFERS]) && !$rfq->selectedOffer)
                                            <a href="{{ route('purchase.rfqs.edit', $rfq) }}" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 ml-1" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        @endcan
                                        @can('delete', $rfq)
                                            @if($rfq->status === App\Models\RFQ::STATUS_DRAFT)
                                            <form action="{{ route('purchase.rfqs.destroy', $rfq) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce RFQ ? Cette action rétablira la demande d\'achat initiale.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($rfqs->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $rfqs->links('pagination::tailwind') }}
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
        const rows = document.querySelectorAll('#rfqsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
