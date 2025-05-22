@extends('layouts.app')

@section('title', "Détails RFQ #{$rfq->id}")

@section('content')
<div class="container-fluid py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Détails de la Demande de Prix <span class="text-somasteel-orange">#{{ $rfq->id }}</span></h1>
        <div>
            @can('update', $rfq)
                 @if(in_array($rfq->status, [App\Models\RFQ::STATUS_DRAFT, App\Models\RFQ::STATUS_SENT, App\Models\RFQ::STATUS_RECEIVING_OFFERS, App\Models\RFQ::STATUS_PROCESSING_OFFERS]) && !$rfq->selectedOffer && $rfq->status !== App\Models\RFQ::STATUS_ORDER_CREATED && $rfq->status !== App\Models\RFQ::STATUS_CLOSED)
                <a href="{{ route('purchase.rfqs.edit', $rfq) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400">
                    <i class="fas fa-edit mr-2"></i> Modifier RFQ
                </a>
                 @endif
            @endcan
            <a href="{{ route('purchase.rfqs.index') }}" class="ml-2 inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fas fa-list-ul mr-2"></i> Liste des RFQs
            </a>
        </div>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 space-y-6">
             {{-- Colonne Informations RFQ (pas de changement majeur ici par rapport à votre version précédente) --}}
             <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Informations du RFQ</h3>
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rfq->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$rfq->status_color.'-100 text-'.$rfq->status_color.'-800' }}">{{ $rfq->status_label }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        {{-- ... (contenu de la dl comme avant) ... --}}
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Demande Achat</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><a href="{{ route('purchase.requests.show', $rfq->purchaseRequest) }}" class="text-somasteel-orange hover:underline">#{{ $rfq->purchaseRequest->id }}</a></dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Demandeur Initial</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rfq->purchaseRequest->user->nom ?? 'N/A' }} {{ $rfq->purchaseRequest->user->prénom ?? '' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Date Limite Offres</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rfq->deadline_for_offers ? $rfq->deadline_for_offers->format('d/m/Y H:i') : 'Non spécifiée' }}</dd>
                        </div>
                        <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Notes Internes</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $rfq->notes ?? 'Aucune note.' }}</dd>
                        </div>
                         @if($rfq->selectedOffer)
                            <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-green-50">
                                <dt class="text-sm font-medium text-green-700">Offre Sélectionnée</dt>
                                <dd class="mt-1 text-sm text-green-900 sm:mt-0 sm:col-span-2">
                                    <p>Fournisseur : <strong class="{{ $rfq->selectedOffer->id == $cheapestOverallOfferId ? 'text-green-600 underline' : '' }}">{{ $rfq->selectedOffer->supplier->company_name }}</strong></p>
                                    <p>Montant Total : <strong>{{ number_format($rfq->selectedOffer->total_offer_price, 2, ',', ' ') }} {{-- devise --}}</strong>
                                       @if($rfq->selectedOffer->id == $cheapestOverallOfferId) <span class="text-xs text-green-600">(Meilleur prix global)</span> @endif
                                    </p>
                                    <div class="mt-3 space-x-2">
                                        @can('selectOffer', [App\Models\Offer::class, $rfq])
                                            @if ($rfq->status !== App\Models\RFQ::STATUS_ORDER_CREATED && $rfq->status !== App\Models\RFQ::STATUS_CLOSED)
                                            <form action="{{ route('rfq.deselectOffer', $rfq) }}" method="POST" onsubmit="return confirm('Annuler la sélection de cette offre ?');" class="inline-block">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md shadow-sm text-yellow-800 bg-yellow-200 hover:bg-yellow-300">Annuler sélection</button>
                                            </form>
                                            @endif
                                        @endcan

                                        @if($rfq->status === App\Models\RFQ::STATUS_SELECTION_DONE)
                                            @can('create', [App\Models\PurchaseOrder::class, $rfq])
                                                <a href="{{ route('purchase.orders.create', $rfq) }}" class="px-3 py-1.5 text-xs font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90">
                                                    <i class="fas fa-file-invoice"></i> Créer BDC
                                                </a>
                                            @endcan
                                        @elseif($rfq->status === App\Models\RFQ::STATUS_ORDER_CREATED)
                                            <span class="px-3 py-1.5 text-xs font-medium rounded-md bg-purple-100 text-purple-800">BDC Créé</span>
                                        @endif
                                    </div>
                                </dd>
                            </div>
                         @endif
                    </dl>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Articles Demandés (Demande Initiale)</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    {{-- ... (contenu des articles demandés comme avant) ... --}}
                    @forelse($purchaseRequestLines as $line)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $line->quantity }} x {{ $line->article->designation ?? 'Article non spécifié' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Réf: {{ $line->article->reference ?? 'N/A' }}
                                </p>
                            </div>
                            @if($line->article && $line->article->description)
                            <p class="mt-1 text-xs text-gray-500">{{ Str::limit($line->article->description, 100) }}</p>
                            @endif
                        </li>
                    @empty
                    <li class="px-4 py-4 sm:px-6 text-sm text-gray-500">Aucun article dans cette demande.</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Fournisseurs Contactés</h3>
                </div>
                 <ul class="divide-y divide-gray-200">
                     {{-- ... (contenu des fournisseurs contactés comme avant, en utilisant $supplier->company_name) ... --}}
                    @forelse($rfq->suppliers as $supplier)
                        <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('purchase.suppliers.show', $supplier) }}" class="text-sm font-medium text-somasteel-orange hover:underline truncate">{{ $supplier->company_name }}</a>
                                @php $supplierHasOffer = $rfq->offers->firstWhere('supplier_id', $supplier->id); @endphp
                                @if($supplierHasOffer)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Offre Reçue</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Attente Offre</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $supplier->contact_email ?? 'Email non fourni' }}</p>
                        </li>
                    @empty
                    <li class="px-4 py-4 sm:px-6 text-sm text-gray-500">Aucun fournisseur contacté pour ce RFQ.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tableau Comparatif des Offres</h3>
                        @can('create', [App\Models\Offer::class, $rfq])
                            @if(!in_array($rfq->status, [App\Models\RFQ::STATUS_SELECTION_DONE, App\Models\RFQ::STATUS_ORDER_CREATED, App\Models\RFQ::STATUS_CLOSED]))
                                <a href="{{ route('rfqs.offers.create', $rfq) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                                    <i class="fas fa-plus-circle mr-2"></i> Ajouter une Offre
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    @if($rfq->offers->isEmpty())
                        <p class="px-6 py-10 text-center text-sm text-gray-500">Aucune offre reçue pour le moment.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 comparison-table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="sticky-col px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Article Demandé</th>
                                        @foreach($rfq->offers as $offer)
                                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider supplier-header
                                                       {{ $rfq->selected_offer_id == $offer->id ? 'table-success-header' : '' }}
                                                       {{ $offer->id == $cheapestOverallOfferId && $rfq->selected_offer_id != $offer->id ? 'bg-green-50 text-green-700 border-b-2 border-green-500' : 'text-gray-600' }}">
                                                <div class="flex flex-col items-center">
                                                    <a href="{{ route('purchase.suppliers.show', $offer->supplier) }}" class="hover:underline font-medium">{{ Str::limit($offer->supplier->company_name, 20) }}</a>
                                                    {{-- Nom de l'entreprise (name) est déjà affiché --}}
                                                    @if($offer->id == $cheapestOverallOfferId)
                                                        <span class="text-xs font-normal text-green-600 mt-0.5">(Meilleur prix global)</span>
                                                    @endif
                                                    <div class="mt-1.5 space-x-1">
                                                        {{-- ... boutons d'action pour l'offre (modifier, supprimer, devis) ... --}}
                                                        @can('update', $offer)
                                                            @if(!in_array($rfq->status, [App\Models\RFQ::STATUS_SELECTION_DONE, App\Models\RFQ::STATUS_ORDER_CREATED, App\Models\RFQ::STATUS_CLOSED]))
                                                                <a href="{{ route('rfqs.offers.edit', [$rfq, $offer]) }}" class="p-1 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-100 rounded-md" title="Modifier Offre"><i class="fas fa-edit fa-xs"></i></a>
                                                            @endif
                                                        @endcan
                                                        @can('delete', $offer)
                                                            @if($rfq->selected_offer_id != $offer->id && !in_array($rfq->status, [App\Models\RFQ::STATUS_SELECTION_DONE, App\Models\RFQ::STATUS_ORDER_CREATED, App\Models\RFQ::STATUS_CLOSED]))
                                                            <form action="{{ route('rfqs.offers.destroy', [$rfq, $offer]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette offre ?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-100 rounded-md" title="Supprimer Offre"><i class="fas fa-trash fa-xs"></i></button>
                                                            </form>
                                                            @endif
                                                        @endcan
                                                        @if($offer->attachment_path)
                                                            <a href="{{ Storage::url($offer->attachment_path) }}" target="_blank" class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-md" title="Voir devis"><i class="fas fa-file-alt fa-xs"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseRequestLines as $prLine)
                                    <tr class="hover:bg-gray-50">
                                        <td class="sticky-col px-4 py-3 text-sm font-medium text-gray-800">
                                            {{ $prLine->article->designation ?? 'N/A' }}
                                            <small class="block text-gray-500">Qté demandée: {{ $prLine->quantity }}</small>
                                        </td>
                                        @foreach($rfq->offers as $offer)
                                            @php
                                                $offerLine = $offer->offerLines->firstWhere('purchase_request_line_id', $prLine->id);
                                                $isCheapestForThisLine = isset($cheapestOfferPerLine[$prLine->id]) &&
                                                                         $offerLine && // S'assurer que l'offre contient cette ligne
                                                                         $offerLine->quantity_offered > 0 && // S'assurer qu'une quantité est offerte
                                                                         in_array($offer->supplier_id, $cheapestOfferPerLine[$prLine->id]['supplier_ids']) &&
                                                                         $offerLine->unit_price == $cheapestOfferPerLine[$prLine->id]['min_price'];
                                            @endphp
                                            <td class="px-4 py-3 text-sm text-center
                                                       {{ $rfq->selected_offer_id == $offer->id ? 'table-success-cell' : '' }}
                                                       {{ $isCheapestForThisLine && $rfq->selected_offer_id != $offer->id ? 'bg-green-50' : '' }}">
                                                @if($offerLine && $offerLine->quantity_offered > 0)
                                                    <p class="{{ $isCheapestForThisLine ? 'text-green-700 font-semibold' : '' }}">
                                                        Qté: {{ $offerLine->quantity_offered }} @ {{ number_format($offerLine->unit_price, 2, ',', ' ') }}
                                                        @if($isCheapestForThisLine) <i class="fas fa-star text-yellow-500 text-xs" title="Meilleur prix pour cet article"></i> @endif
                                                    </p>
                                                    <p class="font-semibold {{ $isCheapestForThisLine ? 'text-green-700' : '' }}">Total: {{ number_format($offerLine->total_price, 2, ',', ' ') }}</p>
                                                    @if($offerLine->description)<p class="text-xs text-gray-500 mt-1" title="{{$offerLine->description}}">Desc: {{ Str::limit($offerLine->description, 25) }}</p>@endif
                                                    @if($offerLine->notes)<p class="text-xs text-gray-500" title="{{$offerLine->notes}}">Note: {{ Str::limit($offerLine->notes, 25) }}</p>@endif
                                                @elseif($offerLine && $offerLine->quantity_offered == 0)
                                                     <span class="text-xs text-gray-400 italic">- Non chiffré -</span>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">- Non proposé -</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    {{-- Ligne pour le total de chaque offre --}}
                                    <tr class="bg-gray-100 font-semibold">
                                        <td class="sticky-col px-4 py-3 text-sm text-gray-800 text-end">TOTAL GLOBAL OFFRE :</td>
                                        @foreach($rfq->offers as $offer)
                                            <td class="px-4 py-3 text-sm text-center
                                                       {{ $rfq->selected_offer_id == $offer->id ? 'table-success-cell font-bold' : '' }}
                                                       {{ $offer->id == $cheapestOverallOfferId && $rfq->selected_offer_id != $offer->id ? 'bg-green-200 text-green-800 font-bold' : '' }}">
                                                {{ number_format($offer->total_offer_price, 2, ',', ' ') }}
                                                @if($offer->id == $cheapestOverallOfferId) <i class="fas fa-trophy text-yellow-500 text-xs" title="Meilleur prix global"></i> @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    {{-- Autres détails de l'offre globale --}}
                                    @php $detailsToShow = ['Termes' => 'terms', "Valide jusqu'au" => 'valid_until_formatted', 'Notes Offre' => 'notes']; @endphp
                                    @foreach($detailsToShow as $label => $field)
                                    <tr class="hover:bg-gray-50">
                                        <td class="sticky-col px-4 py-2 text-xs text-gray-500 text-end">{{$label}}:</td>
                                        @foreach($rfq->offers as $offer)
                                            <td class="px-4 py-2 text-xs text-center text-gray-600 {{ $rfq->selected_offer_id == $offer->id ? 'table-success-cell' : '' }}" title="{{ $field === 'valid_until_formatted' ? ($offer->valid_until ? $offer->valid_until->format('d/m/Y') : '-') : $offer->$field }}">
                                                {{ Str::limit(($field === 'valid_until_formatted' ? ($offer->valid_until ? $offer->valid_until->format('d/m/Y') : '-') : $offer->$field), 35) ?? '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach

                                    @if(!$rfq->hasSelectedOffer() && !in_array($rfq->status, [App\Models\RFQ::STATUS_ORDER_CREATED, App\Models\RFQ::STATUS_CLOSED]))
                                    <tr class="bg-gray-100">
                                        <td class="sticky-col px-4 py-3"></td>
                                        @foreach($rfq->offers as $offer)
                                            <td class="px-4 py-3 text-center">
                                                @can('selectOffer', [App\Models\Offer::class, $rfq])
                                                <form action="{{ route('rfq.selectOffer', $rfq) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="selected_offer_id" value="{{ $offer->id }}">
                                                    <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <i class="fas fa-check-circle mr-1.5"></i> Sélectionner
                                                    </button>
                                                </form>
                                                @endcan
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .comparison-table {
        width: 100%;
    }
    .comparison-table th, .comparison-table td {
        vertical-align: top;
        padding-top: 0.75rem; /* py-3 */
        padding-bottom: 0.75rem; /* py-3 */
    }
    .comparison-table .sticky-col {
        position: -webkit-sticky; /* Safari */
        position: sticky;
        left: 0;
        z-index: 10;
        background-color: #f9fafb; /* bg-gray-50 */
    }
    .comparison-table thead .sticky-col {
        background-color: #f3f4f6; /* bg-gray-100 */
        z-index: 20;
    }
    .comparison-table tr.bg-gray-100 .sticky-col, /* Pour les lignes de totaux et de sélection */
    .comparison-table tr.bg-gray-50 .sticky-col { /* Si une ligne de corps est explicitement bg-gray-50 */
        background-color: #f3f4f6;
    }
    .comparison-table .supplier-header {
        min-width: 160px; /* Ajusté */
        max-width: 220px;
    }
    .table-success-header { /* Offre sélectionnée en-tête */
         background-color: #dcfce7 !important; /* bg-green-100 Tailwind */
         color: #15803d !important; /* text-green-700 Tailwind */
         font-weight: 600;
         border-bottom-width: 2px;
         border-bottom-color: #22c55e !important; /* border-green-500 */
    }
    .table-success-cell { /* Cellules de l'offre sélectionnée */
        background-color: #f0fdf4 !important; /* bg-green-50 Tailwind */
    }
    .badge.bg-purple {
        background-color: #f5f3ff !important; /* bg-purple-50 */
        color: #6d28d9 !important; /* text-purple-700 */
    }
    .btn-xs { padding: 0.1rem 0.3rem; font-size: 0.75rem; }
    .fa-xs { font-size: 0.8em; }

    /* Style pour l'offre la moins chère (non sélectionnée) */
    .cheapest-offer-header {
        background-color: #f0fdf4 !important; /* bg-green-50 */
        border-bottom: 2px solid #86efac !important; /* border-green-300 */
    }
    .cheapest-offer-cell {
         background-color: #fafff5 !important; /* très léger vert */
    }
    .cheapest-line-item {
        /* background-color: #f0fff0; /* très léger vert pour la ligne */
        /* border-left: 3px solid #4CAF50; */ /* ou une bordure */
    }

</style>
@endpush
