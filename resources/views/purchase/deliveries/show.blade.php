@extends('layouts.app')

@section('title', "Détail Réception - BL #{$delivery->delivery_reference}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Détails de la Réception</h1>
            <p class="text-sm text-gray-600">
                Bon de Livraison Fournisseur: <strong class="text-gray-800">#{{ $delivery->delivery_reference }}</strong>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Boutons Edit/Delete si implémentés et autorisés --}}
            {{-- @can('update', $delivery) ... @endcan --}}
            <a href="{{ route('purchase.deliveries.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fas fa-list-ul mr-2"></i> Historique des Réceptions
            </a>
        </div>
    </div>

    @include('layouts.partials.flash_messages')

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between sm:items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Informations de la Réception (ID Interne #{{ $delivery->id }})
                    </h3>
                </div>
                <span class="mt-2 sm:mt-0 px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $delivery->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$delivery->status_color.'-100 text-'.$delivery->status_color.'-800' }}">
                    {{ $delivery->status_label }}
                </span>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Bon de Commande Associé</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <a href="{{ route('purchase.orders.show', $delivery->purchaseOrder) }}" class="text-somasteel-orange hover:underline">#{{ $delivery->purchaseOrder->po_number }}</a>
                    </dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Fournisseur</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $delivery->purchaseOrder->supplier->company_name }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date de Livraison (sur BL)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $delivery->delivery_date->format('d/m/Y') }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Réceptionné par</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $delivery->receivedBy->nom ?? 'N/A' }} {{ $delivery->receivedBy->prénom ?? '' }}</dd>
                </div>
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date d'enregistrement système</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $delivery->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($delivery->notes)
                <div class="py-3 sm:py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Notes sur la Livraison (Globale)</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $delivery->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Articles Réceptionnés pour ce Bon de Livraison
            </h3>
        </div>
        <div class="border-t border-gray-200">
            @if($delivery->deliveryLines->isEmpty())
                <p class="px-4 py-5 sm:px-6 text-sm text-gray-500">Aucun article spécifique enregistré pour cette livraison (vérifiez les notes globales).</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article (BDC)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description (BDC)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté Reçue</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes sur l'article</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmé</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($delivery->deliveryLines as $line)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="font-medium text-gray-800">{{ $line->purchaseOrderLine->article->designation ?? 'N/A' }}</span>
                                    <span class="block text-xs text-gray-500">(Réf: {{ $line->purchaseOrderLine->article->reference ?? 'N/A' }})</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-sm truncate" title="{{ $line->purchaseOrderLine->description }}">{{ Str::limit($line->purchaseOrderLine->description, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $line->quantity_received }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $line->notes }}">{{ Str::limit($line->notes, 30) ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($line->is_confirmed)
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oui</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Non</span>
                                        {{-- Ajouter un bouton de confirmation ici si workflow requis --}}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
