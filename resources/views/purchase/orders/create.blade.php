@extends('layouts.app')

@section('title', "Créer un Bon de Commande")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Créer un Bon de Commande (BDC)</h1>
            <p class="text-sm text-gray-600">
                Basé sur l'offre de <strong class="text-gray-800">{{ $selectedOffer->supplier->company_name }}</strong>
                pour le RFQ <a href="{{ route('purchase.rfqs.show', $rfq) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $rfq->id }}</a>.
            </p>
        </div>
        <a href="{{ route('purchase.rfqs.show', $rfq) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour au RFQ
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées.</span>
        </div>
    @endif

    <form action="{{ route('purchase.orders.store', $rfq) }}" method="POST" class="space-y-8">
        @csrf
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Informations Générales du BDC</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Vérifiez et complétez les informations pour ce bon de commande.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Commande <span class="text-red-500">*</span></label>
                        <input type="date" name="order_date" id="order_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('order_date') border-red-500 @enderror" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                        @error('order_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="expected_delivery_date_global" class="block text-sm font-medium text-gray-700 mb-1">Date de Livraison Globale Attendue</label>
                        <input type="date" name="expected_delivery_date_global" id="expected_delivery_date_global" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('expected_delivery_date_global') border-red-500 @enderror" value="{{ old('expected_delivery_date_global') }}">
                        @error('expected_delivery_date_global') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Termes de Paiement</label>
                        <input type="text" name="payment_terms" id="payment_terms" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('payment_terms') border-red-500 @enderror" value="{{ old('payment_terms', $selectedOffer->terms) }}" placeholder="Ex: Net 30 jours, Paiement à la commande...">
                        @error('payment_terms') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Adresses</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Confirmez les adresses de livraison et de facturation.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de Livraison <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" id="shipping_address" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('shipping_address') border-red-500 @enderror" required>{{ old('shipping_address', $defaultShippingAddress) }}</textarea>
                        @error('shipping_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de Facturation <span class="text-red-500">*</span></label>
                        <textarea name="billing_address" id="billing_address" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('billing_address') border-red-500 @enderror" required>{{ old('billing_address', $defaultBillingAddress) }}</textarea>
                        @error('billing_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails des Articles Commandés</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-4">
                    Ces articles sont basés sur l'offre fournisseur sélectionnée et ne sont pas modifiables à ce stade.
                </p>
                <div class="overflow-x-auto border border-gray-200 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article (Réf. Originale)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description (Offre)</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ligne</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($selectedOffer->offerLines as $offerLine)
                                @if($offerLine->quantity_offered > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-medium text-gray-800">{{ $offerLine->purchaseRequestLine->article->designation ?? 'N/A' }}</span>
                                        <span class="block text-xs text-gray-500">({{ $offerLine->purchaseRequestLine->article->reference ?? 'N/A' }})</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $offerLine->description ?? $offerLine->article->designation }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $offerLine->quantity_offered }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($offerLine->unit_price, 2, ',', ' ') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 font-medium text-right">{{ number_format($offerLine->total_price, 2, ',', ' ') }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="font-semibold text-gray-800">
                                <td colspan="4" class="px-4 py-3 text-right text-sm">TOTAL GÉNÉRAL BDC (HT) :</td>
                                <td class="px-4 py-3 text-right text-sm">{{ number_format($selectedOffer->total_offer_price, 2, ',', ' ') }} {{-- devise --}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Notes</h3>
                <div class="mt-4">
                    <label for="po_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Générales pour le Bon de Commande (Optionnel)</label>
                    <textarea name="po_notes" id="po_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('po_notes') border-red-500 @enderror">{{ old('po_notes') }}</textarea>
                    @error('po_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('purchase.rfqs.show', $rfq) }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-save mr-2"></i> Créer BDC (Brouillon)
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
