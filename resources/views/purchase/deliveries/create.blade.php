@extends('layouts.app')

@section('title', "Enregistrer Réception pour BDC #{$purchaseOrder->po_number}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Enregistrer une Réception</h1>
            <p class="text-sm text-gray-600">
                Pour le Bon de Commande <a href="{{ route('purchase.orders.show', $purchaseOrder) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $purchaseOrder->po_number }}</a>
                de <strong class="text-gray-800">{{ $purchaseOrder->supplier->company_name }}</strong>
            </p>
        </div>
        <a href="{{ route('purchase.deliveries.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour au Dashboard Magasin
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any() && !$errors->has('delivery_lines') && !$errors->has('delivery_lines.*'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs globales du formulaire.</span>
        </div>
    @endif

    <form action="{{ route('purchase.deliveries.store', $purchaseOrder) }}" method="POST" class="space-y-8">
        @csrf
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Informations de la Livraison</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Saisissez les informations du Bon de Livraison (BL) du fournisseur.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_reference" class="block text-sm font-medium text-gray-700 mb-1">Référence BL Fournisseur <span class="text-red-500">*</span></label>
                        <input type="text" name="delivery_reference" id="delivery_reference" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('delivery_reference') border-red-500 @enderror" value="{{ old('delivery_reference') }}" required>
                        @error('delivery_reference') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Livraison (sur BL) <span class="text-red-500">*</span></label>
                        <input type="date" name="delivery_date" id="delivery_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('delivery_date') border-red-500 @enderror" value="{{ old('delivery_date', now()->format('Y-m-d')) }}" required>
                        @error('delivery_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Générales sur la Livraison (Optionnel)</label>
                    <textarea name="delivery_notes" id="delivery_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('delivery_notes') border-red-500 @enderror">{{ old('delivery_notes') }}</textarea>
                    @error('delivery_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Articles Réceptionnés</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-4">
                    Indiquez les quantités reçues pour chaque article de ce bon de livraison.
                </p>
                @error('delivery_lines') <div class="mb-3 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-sm">{{ $message }}</div> @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article (BDC)</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté Cmdée</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté Déjà Reçue</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté Attendue</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="min-width: 120px;">Qté Reçue (ce BL) <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes sur l'article</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $canSubmit = false; @endphp
                            @foreach($purchaseOrder->purchaseOrderLines as $index => $poLine)
                                @if($poLine->quantity_still_expected > 0)
                                @php $canSubmit = true; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 align-top text-sm">
                                        <span class="font-medium text-gray-800">{{ $poLine->article->designation ?? $poLine->description }}</span>
                                        <span class="block text-xs text-gray-500">(Réf: {{ $poLine->article->reference ?? 'N/A' }})</span>
                                    </td>
                                    <td class="px-4 py-3 align-top text-sm text-gray-600 text-center">{{ $poLine->quantity_ordered }}</td>
                                    <td class="px-4 py-3 align-top text-sm text-gray-600 text-center">{{ $poLine->quantity_received ?? 0 }}</td>
                                    <td class="px-4 py-3 align-top text-sm text-gray-800 font-semibold text-center">{{ $poLine->quantity_still_expected }}</td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="number"
                                               name="delivery_lines[{{ $poLine->id }}][quantity_received]"
                                               class="block w-full text-sm text-center border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('delivery_lines.'.$poLine->id.'.quantity_received') border-red-500 @enderror"
                                               value="{{ old('delivery_lines.'.$poLine->id.'.quantity_received', $poLine->quantity_still_expected) }}"
                                               min="0"
                                               max="{{ $poLine->quantity_still_expected }}" required>
                                        @error('delivery_lines.'.$poLine->id.'.quantity_received') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="text"
                                               name="delivery_lines[{{ $poLine->id }}][line_notes]"
                                               class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange"
                                               value="{{ old('delivery_lines.'.$poLine->id.'.line_notes') }}"
                                               placeholder="Ex: Emballage OK, manquant...">
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @if(!$canSubmit && $purchaseOrder->purchaseOrderLines->isNotEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-green-600 bg-green-50">
                                        <i class="fas fa-check-circle mr-1"></i> Toutes les lignes de ce bon de commande semblent avoir été entièrement réceptionnées.
                                    </td>
                                </tr>
                            @elseif($purchaseOrder->purchaseOrderLines->isEmpty())
                                 <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Aucun article sur ce bon de commande.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                 <a href="{{ route('purchase.deliveries.dashboard') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" {{ !$canSubmit ? 'disabled' : '' }}>
                    <i class="fas fa-check-circle mr-2"></i> Enregistrer la Réception
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
