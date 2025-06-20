@extends('layouts.app')

@section('title', "Bon de Commande #{$purchaseOrder->po_number}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bon de Commande <span class="text-somasteel-orange">#{{ $purchaseOrder->po_number }}</span></h1>
        <div class="flex flex-wrap gap-2">
            @can('update', $purchaseOrder)
                 @if(in_array($purchaseOrder->status, [App\Models\PurchaseOrder::STATUS_DRAFT, App\Models\PurchaseOrder::STATUS_PENDING_APPROVAL]))
                    <a href="{{ route('purchase.orders.edit', $purchaseOrder) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </a>
                @endif
            @endcan
            <a href="{{ route('purchase.orders.pdf', $purchaseOrder) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
            <a href="{{ route('purchase.orders.history') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fas fa-list-ul mr-2"></i> Liste des BDC
            </a>
        </div>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informations Générales du BDC
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Détails et statut actuel du bon de commande.
                    </p>
                </div>
                <span class="mt-2 sm:mt-0 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $purchaseOrder->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$purchaseOrder->status_color.'-100 text-'.$purchaseOrder->status_color.'-800' }} {{ $purchaseOrder->status_color == 'teal' ? 'bg-teal-100 text-teal-800' : '' }} {{ $purchaseOrder->status_color == 'purple' ? 'bg-purple-100 text-purple-800' : '' }}">
                    {{ $purchaseOrder->status_label }}
                </span>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Numéro BDC</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $purchaseOrder->po_number }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Fournisseur</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <a href="{{ route('purchase.suppliers.show', $purchaseOrder->supplier) }}" class="text-somasteel-orange hover:underline">{{ $purchaseOrder->supplier->company_name }}</a>
                    </dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date de Commande</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $purchaseOrder->order_date->format('d/m/Y') }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date Liv. Attendue (Globale)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $purchaseOrder->expected_delivery_date_global ? $purchaseOrder->expected_delivery_date_global->format('d/m/Y') : 'N/A' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Créé par</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $purchaseOrder->user->nom ?? 'N/A' }} {{ $purchaseOrder->user->prénom ?? '' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">RFQ d'origine</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><a href="{{ route('purchase.rfqs.show', $purchaseOrder->rfq) }}" class="text-somasteel-orange hover:underline">#{{ $purchaseOrder->rfq->id }}</a></dd>
                </div>
                @if($purchaseOrder->offer)
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Offre Fournisseur Sélectionnée</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Offre #{{ $purchaseOrder->offer_id }} de {{ $purchaseOrder->offer->supplier->company_name ?? 'N/A' }}</dd>
                </div>
                @endif
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Adresse de Livraison</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $purchaseOrder->shipping_address }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Adresse de Facturation</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $purchaseOrder->billing_address }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Termes de Paiement</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $purchaseOrder->payment_terms ?? 'N/A' }}</dd>
                </div>
                 @if($purchaseOrder->notes)
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Notes sur le BDC</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $purchaseOrder->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Actions sur le BDC --}}
        @if(in_array($purchaseOrder->status, [App\Models\PurchaseOrder::STATUS_DRAFT, App\Models\PurchaseOrder::STATUS_APPROVED]))
            @can('sendToSupplier', $purchaseOrder)
            <div class="px-4 py-4 sm:px-6 border-t border-gray-200">
                <form action="{{ route('purchase.orders.send', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Confirmer le marquage de ce BDC comme envoyé au fournisseur ?');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paper-plane mr-2"></i> Marquer comme Envoyé au Fournisseur
                    </button>
                </form>
            </div>
            @endcan
        @elseif($purchaseOrder->status === App\Models\PurchaseOrder::STATUS_SENT_TO_SUPPLIER)
            <div class="px-4 py-4 sm:px-6 border-t border-gray-200">
                <p class="text-sm text-blue-600"><i class="fas fa-check-circle mr-1"></i> BDC envoyé au fournisseur le {{ $purchaseOrder->sent_to_supplier_at ? $purchaseOrder->sent_to_supplier_at->format('d/m/Y H:i') : 'date inconnue' }}.</p>
            </div>
        @endif
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Lignes d'Articles Commandés
            </h3>
        </div>
        <div class="border-t border-gray-200">
             @if($purchaseOrder->purchaseOrderLines->isEmpty())
                <p class="px-4 py-5 sm:px-6 text-sm text-gray-500">Aucun article sur ce bon de commande.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article (Réf. Originale)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description Commandée</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ligne</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes Ligne</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchaseOrder->purchaseOrderLines as $index => $line)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($line->article)
                                    <span class="font-medium text-gray-800">{{ $line->article->designation }}</span>
                                    <span class="block text-xs text-gray-500">({{ $line->article->reference }})</span>
                                @else
                                    {{-- Fallback si l'article a été supprimé ou si pas d'article_id direct --}}
                                    <span class="font-medium text-gray-800">{{ $line->offerLine->purchaseRequestLine->article->designation ?? 'Article spécifique' }}</span>
                                    <span class="block text-xs text-gray-500">({{ $line->offerLine->purchaseRequestLine->article->reference ?? 'N/A' }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-sm truncate" title="{{ $line->description }}">{{ Str::limit($line->description, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $line->quantity_ordered }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">{{ number_format($line->unit_price, 2, ',', ' ') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium text-right">{{ number_format($line->total_price, 2, ',', ' ') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $line->notes }}">{{ Str::limit($line->notes, 30) ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr class="font-semibold text-gray-800">
                            <td colspan="5" class="px-6 py-3 text-right text-sm">MONTANT TOTAL BDC (HT) :</td>
                            <td class="px-6 py-3 text-right text-sm">{{ number_format($purchaseOrder->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
