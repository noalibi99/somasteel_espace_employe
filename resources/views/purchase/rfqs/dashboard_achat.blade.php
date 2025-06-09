@extends('layouts.app')

@section('title', "Dashboard Achat - RFQ")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord Service Achat</h1>
        <a href="{{ route('purchase.rfqs.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-list-ul mr-2"></i> Voir tous les Demandes de Prix
        </a>
    </div>
    <p class="text-sm text-gray-600">Liste des demandes d'achat approuvées attendant la création d'une Demande de Prix.</p>

    @include('layouts.partials.flash_messages')

    <div class="relative w-full md:w-1/3">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher par ID, demandeur..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Demandes d'Achat Approuvées
            </h3>
        </div>
        <div class="border-t border-gray-200">
            @if($approvedRequests->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucune demande d'achat approuvée en attente de création de RFQ pour le moment.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="approvedRequestsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Demande</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Approbation</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut DA</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($approvedRequests as $pr)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.requests.show', $pr) }}">#{{ $pr->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pr->user->nom ?? 'N/A' }} {{ $pr->user->prénom ?? '' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-md truncate" title="{{ $pr->description }}">{{ Str::limit($pr->description, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pr->validated_at ? $pr->validated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full {{ $pr->status_color == 'primary' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas fa-cogs"></i> {{-- Icône pour 'Approuvée (Attente Achat)' --}}
                                            {{ $pr->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if(!$pr->rfq && $pr->status == App\Models\PurchaseRequest::STATUS_APPROVED)
                                            @can('create', [App\Models\RFQ::class, $pr])
                                            <a href="{{ route('purchase.rfq.create', $pr) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                                                <i class="fas fa-plus-circle mr-1"></i> Créer RFQ
                                            </a>
                                            @endcan
                                        @elseif($pr->rfq)
                                            <a href="{{ route('purchase.rfqs.show', $pr->rfq) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                               <i class="fas fa-eye mr-1"></i> Voir RFQ #{{$pr->rfq->id}}
                                            </a>
                                        @else
                                             <span class="text-xs text-gray-400 italic">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($approvedRequests->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $approvedRequests->links('pagination::tailwind') }}
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
        const rows = document.querySelectorAll('#approvedRequestsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
