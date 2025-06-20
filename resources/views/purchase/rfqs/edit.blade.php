@extends('layouts.app')

@section('title', "Modifier RFQ #{$rfq->id}")

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier la Demande de Prix (RFQ) <span class="text-somasteel-orange">#{{ $rfq->id }}</span></h1>
         <a href="{{ route('purchase.rfqs.show', $rfq) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Annuler
        </a>
    </div>
    <p class="text-sm text-gray-600 mb-4">Pour la demande d'achat : <a href="{{ route('purchase.requests.show', $purchaseRequest) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $purchaseRequest->id }}</a> par {{ $purchaseRequest->user->nom }} {{ $purchaseRequest->user->prénom }}.</p>
    <p class="text-sm text-gray-600 mb-6">Statut actuel du RFQ : <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rfq->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$rfq->status_color.'-100 text-'.$rfq->status_color.'-800' }}">{{ $rfq->status_label }}</span></p>


    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
     @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées.</span>
        </div>
    @endif

    <form action="{{ route('purchase.rfqs.update', $rfq) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails de la Demande d'Achat Initiale (Non modifiable)</h3>
                 <div class="mt-2 text-sm text-gray-700 space-y-2">
                    <p><strong>Description:</strong> {{ $purchaseRequest->description }}</p>
                    <p class="font-medium">Articles Demandés :</p>
                    <ul class="list-disc list-inside pl-4 space-y-1">
                        @foreach($purchaseRequest->lines as $line)
                            <li>
                                {{ $line->quantity }} x
                                @if($line->article)
                                    <strong>{{ $line->article->designation }}</strong> (Réf: {{ $line->article->reference ?? 'N/A' }})
                                @else
                                    Article non spécifié (ID: {{ $line->article_id }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                 <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations du RFQ</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner les Fournisseurs pour le RFQ <span class="text-red-500">*</span></label>
                        @php $selectedRfqSuppliers = old('suppliers', $rfq->suppliers->pluck('id')->toArray()); @endphp
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3">
                            @foreach($suppliers as $supplier)
                            <div class="flex items-center p-2 rounded-md hover:bg-gray-50 transition-colors">
                                <input class="h-4 w-4 text-somasteel-orange border-gray-300 rounded focus:ring-somasteel-orange"
                                       type="checkbox"
                                       name="suppliers[]" {{-- Note: le nom est 'suppliers[]' pour la MAJ --}}
                                       value="{{ $supplier->id }}"
                                       id="supplier_rfq_edit_{{ $supplier->id }}"
                                       {{ in_array($supplier->id, $selectedRfqSuppliers) ? 'checked' : '' }}>
                                <label for="supplier_rfq_edit_{{ $supplier->id }}" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                                    {{ $supplier->name }} <span class="text-gray-500">({{ $supplier->contact_email ?? 'Email non fourni' }})</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('suppliers') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('suppliers.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deadline_for_offers_edit" class="block text-sm font-medium text-gray-700 mb-1">Date limite pour réception des offres</label>
                        <input type="datetime-local" name="deadline_for_offers" id="deadline_for_offers_edit"
                               class="w-full md:w-2/3 border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('deadline_for_offers') border-red-500 @enderror"
                               value="{{ old('deadline_for_offers', $rfq->deadline_for_offers ? $rfq->deadline_for_offers->format('Y-m-d\TH:i') : '') }}">
                        @error('deadline_for_offers') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="notes_edit" class="block text-sm font-medium text-gray-700 mb-1">Notes Internes (optionnel)</label>
                        <textarea name="notes" id="notes_edit" rows="3" class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-none @error('notes') border-red-500 @enderror">{{ old('notes', $rfq->notes) }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- La section email n'est pas modifiable ici, car l'email est envoyé une seule fois à la création si coché --}}
        {{-- Si vous souhaitez permettre de renvoyer/modifier l'email, il faudrait une action dédiée --}}

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('purchase.rfqs.show', $rfq) }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-save mr-2"></i> Mettre à jour RFQ
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
