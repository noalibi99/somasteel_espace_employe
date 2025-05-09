@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nouvelle Demande d'Achat</h2>
    
    <form method="POST" action="{{ route('purchase.requests.store') }}" id="purchase-request-form">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">Informations Générales</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="description">Description de la demande *</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Articles Demandés</div>
            <div class="card-body">
                <div id="items-container">
                    <div class="item-row mb-4 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type d'article *</label>
                                    <select name="items[0][type]" class="form-control item-type" onchange="toggleItemInput(this)" required>
                                        <option value="">-- Sélectionner --</option>
                                        <option value="existing" selected>Article existant</option>
                                        <option value="new">Nouvel article</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 existing-item">
                                <div class="form-group">
                                    <label>Article existant *</label>
                                    <select name="items[0][article_id]" class="form-control existing-article-select" required>
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
                            </div>
                            
                            <div class="col-md-6 new-item d-none">
                                <div class="form-group">
                                    <label>Référence *</label>
                                    <input type="text" name="items[0][reference]" class="form-control new-reference" placeholder="Référence de l'article">
                                </div>
                                <div class="form-group">
                                    <label>Désignation *</label>
                                    <input type="text" name="items[0][designation]" class="form-control new-designation" placeholder="Désignation de l'article">
                                </div>
                                <div class="form-group">
                                    <label>Description *</label>
                                    <textarea name="items[0][new_description]" class="form-control new-description" placeholder="Description détaillée"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Numéro de série</label>
                                    <input type="text" name="items[0][sn]" class="form-control new-sn" placeholder="SN123456">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Quantité *</label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity" placeholder="1" min="1" required>
                                </div>
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item mb-3">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="add-item" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Ajouter un article
                </button>
            </div>
        </div>
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Soumettre la demande
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    let itemCount = 1;
    
    // Fonction pour basculer entre article existant et nouveau
    window.toggleItemInput = function(selectElement) {
        const row = selectElement.closest('.item-row');
        const isNew = selectElement.value === 'new';
        
        // Basculer la visibilité
        row.querySelector('.existing-item').classList.toggle('d-none', isNew);
        row.querySelector('.new-item').classList.toggle('d-none', !isNew);
        
        // Gérer les champs obligatoires
        const existingSelect = row.querySelector('.existing-article-select');
        const newInputs = row.querySelectorAll('.new-reference, .new-designation, .new-description');
        
        existingSelect.required = !isNew;
        newInputs.forEach(input => input.required = isNew);
    };
    
    // Ajouter un nouvel item
    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'item-row mb-4 p-3 border rounded';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Type d'article *</label>
                        <select name="items[${itemCount}][type]" class="form-control item-type" onchange="toggleItemInput(this)" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="existing" selected>Article existant</option>
                            <option value="new">Nouvel article</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6 existing-item">
                    <div class="form-group">
                        <label>Article existant *</label>
                        <select name="items[${itemCount}][article_id]" class="form-control existing-article-select" required>
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
                </div>
                
                <div class="col-md-6 new-item d-none">
                    <div class="form-group">
                        <label>Référence *</label>
                        <input type="text" name="items[${itemCount}][reference]" class="form-control new-reference" placeholder="Référence de l'article" required>
                    </div>
                    <div class="form-group">
                        <label>Désignation *</label>
                        <input type="text" name="items[${itemCount}][designation]" class="form-control new-designation" placeholder="Désignation de l'article" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="items[${itemCount}][new_description]" class="form-control new-description" placeholder="Description détaillée" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Numéro de série</label>
                        <input type="text" name="items[${itemCount}][sn]" class="form-control new-sn" placeholder="SN123456">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Quantité *</label>
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control quantity" placeholder="1" min="1" required>
                    </div>
                </div>
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item mb-3">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCount++;
    });
    
    // Supprimer un item
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
    
    // Validation du formulaire
    document.getElementById('purchase-request-form').addEventListener('submit', function(e) {
        let isValid = true;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const typeSelect = row.querySelector('.item-type');
            if (!typeSelect.value) {
                alert('Veuillez sélectionner un type pour chaque article');
                isValid = false;
                return;
            }
            
            if (typeSelect.value === 'existing') {
                if (!row.querySelector('.existing-article-select').value) {
                    alert('Veuillez sélectionner un article existant');
                    isValid = false;
                    return;
                }
            } else {
                const requiredInputs = row.querySelectorAll('.new-reference, .new-designation, .new-description');
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        alert('Veuillez remplir tous les champs obligatoires pour les nouveaux articles');
                        isValid = false;
                        return;
                    }
                });
            }
            
            if (!row.querySelector('.quantity').value || parseInt(row.querySelector('.quantity').value) < 1) {
                alert('Veuillez entrer une quantité valide (minimum 1)');
                isValid = false;
                return;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<style>
.d-none {
    display: none;
}
.item-row {
    background-color: #f8f9fa;
}
.remove-item {
    padding: 0.375rem 0.75rem;
}
</style>
@endsection