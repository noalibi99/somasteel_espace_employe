@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nouvelle Demande d'Achat</h2>
    
    <form method="POST" action="{{ route('purchase.requests.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="justification">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
        </div>
        
        <h4 class="mt-4">Articles Demandés</h4>
        
        <div id="items-container">
            <div class="item-row mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="items[0][description]" class="form-control" placeholder="Description" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[0][quantity]" class="form-control" placeholder="Quantité" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger remove-item">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="button" id="add-item" class="btn btn-secondary mb-3">Ajouter un article</button>
        
        <button type="submit" class="btn btn-primary">Soumettre la demande</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    let itemCount = 1;
    
    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'item-row mb-3';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="items[${itemCount}][description]" class="form-control" placeholder="Description" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="items[${itemCount}][quantity]" class="form-control" placeholder="Quantité" min="1" required>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger remove-item">Supprimer</button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        itemCount++;
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
            } else {
                alert('Vous devez avoir au moins un article.');
            }
        }
    });
});
</script>
@endsection