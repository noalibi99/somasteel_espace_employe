@extends('layouts.app')

@section('title', "Modifier l'Offre de {$offer->supplier->company_name}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'Offre Fournisseur</h1>
            <p class="text-sm text-gray-600">
                Fournisseur: <strong class="text-gray-800">{{ $offer->supplier->company_name }}</strong> | Pour RFQ <a href="{{ route('purchase.rfqs.show', $rfq) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $rfq->id }}</a>
            </p>
        </div>
         <a href="{{ route('purchase.rfqs.show', $rfq) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour au RFQ
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any() && !$errors->has('offer_lines') && !$errors->has('offer_lines.*'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs globales du formulaire.</span>
        </div>
    @endif

    <form action="{{ route('rfqs.offers.update', [$rfq, $offer]) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Informations Générales de l'Offre</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Mettez à jour les détails généraux de l'offre.
                </p>
                 <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <input type="text" class="block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm p-2 focus:ring-0 focus:border-gray-300" value="{{ $offer->supplier->company_name }}" disabled readonly>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Offre Valide Jusqu'au</label>
                        <input type="date" name="valid_until" id="valid_until" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('valid_until') border-red-500 @enderror" value="{{ old('valid_until', $offer->valid_until ? $offer->valid_until->format('Y-m-d') : '') }}">
                        @error('valid_until') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Termes et Conditions</label>
                    <textarea name="terms" id="terms" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('terms') border-red-500 @enderror">{{ old('terms', $offer->terms) }}</textarea>
                    @error('terms') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="offer_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Générales sur l'Offre</label>
                    <textarea name="offer_notes" id="offer_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('offer_notes') border-red-500 @enderror">{{ old('offer_notes', $offer->notes) }}</textarea>
                    @error('offer_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Pièce Jointe</label>
                    <input type="file" name="attachment" id="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-somasteel-orange/10 file:text-somasteel-orange hover:file:bg-somasteel-orange/20 @error('attachment') border-red-500 rounded-md p-1 @enderror">
                    @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @if($offer->attachment_path)
                    <p class="mt-1 text-xs text-gray-500">Fichier actuel: <a href="{{ Storage::url($offer->attachment_path) }}" target="_blank" class="text-somasteel-orange hover:underline">Voir le fichier</a>. Laisser vide pour conserver.</p>
                    @else
                    <p class="mt-1 text-xs text-gray-500">Aucun fichier joint actuellement.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Lignes d'Articles de l'Offre <span class="text-red-500">*</span></h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-4">
                    Ajustez les quantités et prix proposés par le fournisseur.
                </p>
                @error('offer_lines') <div class="mb-3 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-sm">{{ $message }}</div> @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="offer-lines-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:30%">Article Demandé (Original)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:15%">Qté Offerte <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:15%">Prix Unitaire <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:20%">Description (Fournisseur)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width:20%">Notes Ligne</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $linesData = old('offer_lines', $offer->offerLines->mapWithKeys(function ($line) {
                                return [$line->purchase_request_line_id => [ // Utiliser purchase_request_line_id comme clé pour faciliter la recherche
                                    'id' => $line->id,
                                    'purchase_request_line_id' => $line->purchase_request_line_id,
                                    'article_id' => $line->article_id,
                                    'description' => $line->description,
                                    'quantity_offered' => $line->quantity_offered,
                                    'unit_price' => $line->unit_price,
                                    'line_notes' => $line->notes,
                                ]];
                            })->all());
                        @endphp

                        @foreach($purchaseRequestLines as $index => $prLine)
                            @php
                                $currentOfferLineData = $linesData[$prLine->id] ?? null;
                                $offerLineLoopIndex = $prLine->id; // Utiliser l'ID de la ligne PR pour l'index du tableau de formulaire
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 align-top">
                                    <p class="text-sm font-medium text-gray-800">{{ $prLine->article->designation ?? 'Article N/A' }}</p>
                                    <p class="text-xs text-gray-500">Qté demandée: {{ $prLine->quantity }} (Réf: {{ $prLine->article->reference ?? 'N/A' }})</p>
                                    <input type="hidden" name="offer_lines[{{ $offerLineLoopIndex }}][id]" value="{{ $currentOfferLineData['id'] ?? '' }}">
                                    <input type="hidden" name="offer_lines[{{ $offerLineLoopIndex }}][purchase_request_line_id]" value="{{ $prLine->id }}">
                                    <input type="hidden" name="offer_lines[{{ $offerLineLoopIndex }}][article_id]" value="{{ $currentOfferLineData['article_id'] ?? $prLine->article_id }}">
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input type="number" name="offer_lines[{{ $offerLineLoopIndex }}][quantity_offered]"
                                           class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$offerLineLoopIndex}.quantity_offered") border-red-500 @enderror"
                                           value="{{ $currentOfferLineData['quantity_offered'] ?? $prLine->quantity }}" min="0" required>
                                    @error("offer_lines.{$offerLineLoopIndex}.quantity_offered") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input type="number" name="offer_lines[{{ $offerLineLoopIndex }}][unit_price]"
                                           class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$offerLineLoopIndex}.unit_price") border-red-500 @enderror"
                                           value="{{ $currentOfferLineData['unit_price'] ?? '0.00' }}" step="0.01" min="0" required>
                                     @error("offer_lines.{$offerLineLoopIndex}.unit_price") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input type="text" name="offer_lines[{{ $offerLineLoopIndex }}][description]"
                                           class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange"
                                           value="{{ $currentOfferLineData['description'] ?? '' }}" placeholder="Description article fournisseur">
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input type="text" name="offer_lines[{{ $offerLineLoopIndex }}][line_notes]"
                                           class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange"
                                           value="{{ $currentOfferLineData['line_notes'] ?? '' }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
                    <i class="fas fa-save mr-2"></i> Mettre à jour l'Offre
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
