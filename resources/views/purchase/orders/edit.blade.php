@extends('layouts.app')

@section('title', "Modifier BDC #{$purchaseOrder->po_number}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier le Bon de Commande <span class="text-somasteel-orange">#{{ $purchaseOrder->po_number }}</span></h1>
            <p class="text-sm text-gray-600">
                Fournisseur: <strong class="text-gray-800">{{ $purchaseOrder->supplier->company_name }}</strong>
            </p>
        </div>
        <a href="{{ route('purchase.orders.show', $purchaseOrder) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Annuler les modifications
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées.</span>
        </div>
    @endif

    <form action="{{ route('purchase.orders.update', $purchaseOrder) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Informations Générales du BDC</h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Statut actuel: <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $purchaseOrder->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$purchaseOrder->status_color.'-100 text-'.$purchaseOrder->status_color.'-800' }}">{{ $purchaseOrder->status_label }}</span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="po_number_display" class="block text-sm font-medium text-gray-700 mb-1">Numéro BDC</label>
                        <input type="text" id="po_number_display" class="block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm p-2 focus:ring-0 focus:border-gray-300" value="{{ $purchaseOrder->po_number }}" readonly>
                    </div>
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Commande <span class="text-red-500">*</span></label>
                        <input type="date" name="order_date" id="order_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('order_date') border-red-500 @enderror" value="{{ old('order_date', $purchaseOrder->order_date->format('Y-m-d')) }}" required>
                        @error('order_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="expected_delivery_date_global" class="block text-sm font-medium text-gray-700 mb-1">Date de Livraison Globale Attendue</label>
                        <input type="date" name="expected_delivery_date_global" id="expected_delivery_date_global" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('expected_delivery_date_global') border-red-500 @enderror" value="{{ old('expected_delivery_date_global', $purchaseOrder->expected_delivery_date_global ? $purchaseOrder->expected_delivery_date_global->format('Y-m-d') : '') }}">
                        @error('expected_delivery_date_global') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">Termes de Paiement</label>
                        <input type="text" name="payment_terms" id="payment_terms" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('payment_terms') border-red-500 @enderror" value="{{ old('payment_terms', $purchaseOrder->payment_terms) }}" placeholder="Ex: Net 30 jours">
                        @error('payment_terms') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
             <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Adresses</h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Mettez à jour les adresses si nécessaire.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de Livraison <span class="text-red-500">*</span></label>
                        <textarea name="shipping_address" id="shipping_address" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('shipping_address') border-red-500 @enderror" required>{{ old('shipping_address', $purchaseOrder->shipping_address) }}</textarea>
                        @error('shipping_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse de Facturation <span class="text-red-500">*</span></label>
                        <textarea name="billing_address" id="billing_address" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('billing_address') border-red-500 @enderror" required>{{ old('billing_address', $purchaseOrder->billing_address) }}</textarea>
                        @error('billing_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails des Articles Commandés</h3>
                <p class="mt-1 text-sm text-yellow-600 bg-yellow-50 p-3 rounded-md mb-4">
                    <i class="fas fa-info-circle mr-1"></i> La modification des lignes d'articles n'est pas permise à ce stade pour garantir la cohérence avec l'offre validée. Pour des changements majeurs, envisagez d'annuler ce BDC (si en brouillon) et d'en recréer un, ou de gérer un avenant (fonctionnalité non implémentée).
                </p>
                <div class="overflow-x-auto border border-gray-200 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">PU</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchaseOrder->purchaseOrderLines as $line)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-medium text-gray-800">{{ $line->article->designation ?? $line->description }}</span>
                                    @if($line->article) <span class="block text-xs text-gray-500">({{ $line->article->reference }})</span> @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $line->description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $line->quantity_ordered }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($line->unit_price, 2, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800 font-medium text-right">{{ number_format($line->total_price, 2, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="font-semibold text-gray-800">
                                <td colspan="4" class="px-4 py-3 text-right text-sm">TOTAL GÉNÉRAL BDC (HT) :</td>
                                <td class="px-4 py-3 text-right text-sm">{{ number_format($purchaseOrder->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
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
                    <label for="po_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Générales pour le Bon de Commande</label>
                    <textarea name="po_notes" id="po_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('po_notes') border-red-500 @enderror">{{ old('po_notes', $purchaseOrder->notes) }}</textarea>
                    @error('po_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('purchase.orders.show', $purchaseOrder) }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-save mr-2"></i> Mettre à jour BDC
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
