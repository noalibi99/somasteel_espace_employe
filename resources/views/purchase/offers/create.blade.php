@extends('layouts.app')

@section('title', "Ajouter une Offre au RFQ #{$rfq->id}")

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6"> {{-- max-w-5xl pour un peu plus de largeur --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ajouter une Offre Fournisseur</h1>
            <p class="text-sm text-gray-600">
                Pour la Demande de Prix (RFQ) <a href="{{ route('purchase.rfqs.show', $rfq) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $rfq->id }}</a>
            </p>
        </div>
        <a href="{{ route('purchase.rfqs.show', $rfq) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour au RFQ
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any() && !$errors->has('offer_lines') && !$errors->has('offer_lines.*')) {{-- Affiche les erreurs générales du formulaire --}}
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs globales du formulaire. Les erreurs spécifiques aux lignes sont affichées ci-dessous.</span>
        </div>
    @endif

    <form action="{{ route('rfqs.offers.store', $rfq) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Informations Générales de l'Offre</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Renseignez les détails généraux de l'offre reçue du fournisseur.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('supplier_id') border-red-500 @enderror" required>
                            <option value="">-- Sélectionner un fournisseur --</option>
                            @foreach($availableSuppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @if($availableSuppliers->isEmpty() && $rfq->suppliers->isNotEmpty() && !$errors->has('supplier_id'))
                        <p class="mt-1 text-xs text-blue-600">Tous les fournisseurs contactés pour ce RFQ ont déjà une offre enregistrée.</p>
                        @elseif($rfq->suppliers->isEmpty())
                        <p class="mt-1 text-xs text-yellow-600">Aucun fournisseur n'est associé à ce RFQ. Veuillez d'abord en ajouter via la modification du RFQ.</p>
                        @endif
                    </div>
                    <div>
                        <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">Offre Valide Jusqu'au</label>
                        <input type="date" name="valid_until" id="valid_until" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('valid_until') border-red-500 @enderror" value="{{ old('valid_until') }}">
                        @error('valid_until') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">Termes et Conditions (Optionnel)</label>
                    <textarea name="terms" id="terms" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('terms') border-red-500 @enderror">{{ old('terms') }}</textarea>
                    @error('terms') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="offer_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Générales sur l'Offre (Optionnel)</label>
                    <textarea name="offer_notes" id="offer_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('offer_notes') border-red-500 @enderror">{{ old('offer_notes') }}</textarea>
                    @error('offer_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Pièce Jointe (Devis du fournisseur, PDF, etc.)</label>
                    <input type="file" name="attachment" id="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-somasteel-orange/10 file:text-somasteel-orange hover:file:bg-somasteel-orange/20 @error('attachment') border-red-500 rounded-md p-1 @enderror">
                    @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                     <p class="mt-1 text-xs text-gray-500">Max. 5MB. Formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Lignes d'Articles de l'Offre <span class="text-red-500">*</span></h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-4">
                    Saisissez les quantités et prix proposés par le fournisseur pour chaque article de la demande initiale.
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
                            @if(old('offer_lines'))
                                @foreach(old('offer_lines') as $index => $oldLine)
                                    @php $prLine = $purchaseRequestLines->firstWhere('id', $oldLine['purchase_request_line_id']); @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 align-top">
                                            <p class="text-sm font-medium text-gray-800">{{ $prLine->article->designation ?? 'Article N/A' }}</p>
                                            <p class="text-xs text-gray-500">Qté demandée: {{ $prLine->quantity }} (Réf: {{ $prLine->article->reference ?? 'N/A' }})</p>
                                            <input type="hidden" name="offer_lines[{{ $index }}][purchase_request_line_id]" value="{{ $oldLine['purchase_request_line_id'] }}">
                                            <input type="hidden" name="offer_lines[{{ $index }}][article_id]" value="{{ $oldLine['article_id'] ?? $prLine->article_id }}">
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="number" name="offer_lines[{{ $index }}][quantity_offered]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.quantity_offered") border-red-500 @enderror" value="{{ $oldLine['quantity_offered'] }}" min="0" required>
                                            @error("offer_lines.{$index}.quantity_offered") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="number" name="offer_lines[{{ $index }}][unit_price]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.unit_price") border-red-500 @enderror" value="{{ $oldLine['unit_price'] }}" step="0.01" min="0" required>
                                            @error("offer_lines.{$index}.unit_price") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="text" name="offer_lines[{{ $index }}][description]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.description") border-red-500 @enderror" value="{{ $oldLine['description'] }}" placeholder="Description article fournisseur">
                                            @error("offer_lines.{$index}.description") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            <input type="text" name="offer_lines[{{ $index }}][line_notes]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.line_notes") border-red-500 @enderror" value="{{ $oldLine['line_notes'] }}">
                                            @error("offer_lines.{$index}.line_notes") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($purchaseRequestLines as $index => $prLine)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 align-top">
                                        <p class="text-sm font-medium text-gray-800">{{ $prLine->article->designation ?? 'Article N/A' }}</p>
                                        <p class="text-xs text-gray-500">Qté demandée: {{ $prLine->quantity }} (Réf: {{ $prLine->article->reference ?? 'N/A' }})</p>
                                        <input type="hidden" name="offer_lines[{{ $index }}][purchase_request_line_id]" value="{{ $prLine->id }}">
                                        <input type="hidden" name="offer_lines[{{ $index }}][article_id]" value="{{ $prLine->article_id }}">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="number" name="offer_lines[{{ $index }}][quantity_offered]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.quantity_offered") border-red-500 @enderror" value="{{ old("offer_lines.{$index}.quantity_offered", $prLine->quantity) }}" min="0" required>
                                         @error("offer_lines.{$index}.quantity_offered") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="number" name="offer_lines[{{ $index }}][unit_price]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error("offer_lines.{$index}.unit_price") border-red-500 @enderror" value="{{ old("offer_lines.{$index}.unit_price", '0.00') }}" step="0.01" min="0" required>
                                        @error("offer_lines.{$index}.unit_price") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="text" name="offer_lines[{{ $index }}][description]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange" value="{{ old("offer_lines.{$index}.description") }}" placeholder="Si différent de l'original">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="text" name="offer_lines[{{ $index }}][line_notes]" class="block w-full text-sm border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange" value="{{ old("offer_lines.{$index}.line_notes") }}">
                                    </td>
                                </tr>
                                @endforeach
                            @endif
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
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" {{ ($availableSuppliers->isEmpty() && !$rfq->suppliers->isEmpty() && !$errors->has('supplier_id')) ? 'disabled' : '' }}>
                    <i class="fas fa-save mr-2"></i> Enregistrer l'Offre
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
