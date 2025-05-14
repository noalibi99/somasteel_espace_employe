@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-8">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-6">Nouvelle Demande d'Achat</h2>

    <form method="POST" action="{{ route('purchase.requests.store') }}" id="purchase-request-form" class="space-y-8">
        @csrf

        {{-- Informations Générales --}}
        <section class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Informations Générales</h3>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description de la demande <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-none transition"></textarea>
            </div>
        </section>

        {{-- Articles Demandés --}}
        <section class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Articles Demandés</h3>

            <div id="items-container" class="space-y-6">
                <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-12 gap-4 items-end">
                        {{-- Type d'article --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type d'article <span class="text-red-500">*</span></label>
                            <select name="items[0][type]" class="item-type block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" onchange="toggleItemInput(this)" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="existing" selected>Article existant</option>
                                <option value="new">Nouvel article</option>
                            </select>
                        </div>

                        {{-- Article existant --}}
                        <div class="col-span-12 md:col-span-6 existing-item">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Article existant <span class="text-red-500">*</span></label>
                            <select name="items[0][article_id]" class="existing-article-select block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" required>
                                <option value="">-- Choisir un article --</option>
                                @foreach($approvedArticles as $article)
                                    <option value="{{ $article->id }}" data-reference="{{ $article->reference }}" 
                                        data-designation="{{ $article->designation }}" 
                                        data-description="{{ $article->description }}">
                                        {{ $article->reference }} - {{ $article->designation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nouvel article --}}
                        <div class="col-span-12 md:col-span-6 new-item hidden space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Référence <span class="text-red-500">*</span></label>
                                <input type="text" name="items[0][reference]" class="new-reference block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="Référence de l'article">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Désignation <span class="text-red-500">*</span></label>
                                <input type="text" name="items[0][designation]" class="new-designation block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="Désignation de l'article">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                <textarea name="items[0][new_description]" class="new-description block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-none" placeholder="Description détaillée"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                                <input type="text" name="items[0][sn]" class="new-sn block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="SN123456">
                            </div>
                        </div>

                        {{-- Quantité --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantité <span class="text-red-500">*</span></label>
                            <input type="number" name="items[0][quantity]" class="quantity block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="1" min="1" required>
                        </div>

                        {{-- Remove button --}}
                        <div class="col-span-12 md:col-span-1 flex items-end">
                            <button type="button" class="remove-item inline-flex items-center justify-center p-2 rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500" title="Supprimer cet article">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-item" class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fas fa-plus mr-2"></i> Ajouter un article
            </button>
        </section>

        <div class="text-center">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-somasteel-orange text-white font-semibold rounded-md shadow hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange transition mt-6">
                <i class="fas fa-paper-plane mr-2"></i> Soumettre la demande
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    let itemCount = 1;

    window.toggleItemInput = function(selectElement) {
        const row = selectElement.closest('.item-row');
        const isNew = selectElement.value === 'new';

        row.querySelector('.existing-item').classList.toggle('hidden', isNew);
        row.querySelector('.new-item').classList.toggle('hidden', !isNew);

        const existingSelect = row.querySelector('.existing-article-select');
        const newInputs = row.querySelectorAll('.new-reference, .new-designation, .new-description');

        existingSelect.required = !isNew;
        newInputs.forEach(input => input.required = isNew);
    };

    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'item-row bg-gray-50 p-4 rounded-lg border border-gray-200 mt-6';
        newItem.innerHTML = `
            <div class="grid grid-cols-12 gap-4 items-end">
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'article <span class="text-red-500">*</span></label>
                    <select name="items[${itemCount}][type]" class="item-type block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" onchange="toggleItemInput(this)" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="existing" selected>Article existant</option>
                        <option value="new">Nouvel article</option>
                    </select>
                </div>

                <div class="col-span-12 md:col-span-6 existing-item">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Article existant <span class="text-red-500">*</span></label>
                    <select name="items[${itemCount}][article_id]" class="existing-article-select block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" required>
                        <option value="">-- Choisir un article --</option>
                        @foreach($approvedArticles as $article)
                            <option value="{{ $article->id }}" data-reference="{{ $article->reference }}" 
                                data-designation="{{ $article->designation }}" 
                                data-description="{{ $article->description }}">
                                {{ $article->reference }} - {{ $article->designation }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-12 md:col-span-6 new-item hidden space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Référence <span class="text-red-500">*</span></label>
                        <input type="text" name="items[${itemCount}][reference]" class="new-reference block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="Référence de l'article">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Désignation <span class="text-red-500">*</span></label>
                        <input type="text" name="items[${itemCount}][designation]" class="new-designation block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="Désignation de l'article">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="items[${itemCount}][new_description]" class="new-description block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-none" placeholder="Description détaillée"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                        <input type="text" name="items[${itemCount}][sn]" class="new-sn block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="SN123456">
                    </div>
                </div>

                <div class="col-span-12 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemCount}][quantity]" class="quantity block w-full rounded-md border border-gray-300 shadow-sm p-2 focus:ring-2 focus:ring-somasteel-orange focus:border-somasteel-orange" placeholder="1" min="1" required>
                </div>

                <div class="col-span-12 md:col-span-1 flex items-end">
                    <button type="button" class="remove-item inline-flex items-center justify-center p-2 rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500" title="Supprimer cet article">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCount++;
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const itemRow = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                itemRow.remove();
            } else {
                alert('Vous devez avoir au moins un article.');
            }
        }
    });

    // Optional: Initialize toggle for the first row on page load
    document.querySelectorAll('.item-type').forEach(select => toggleItemInput(select));
});
</script>
@endpush
@endsection