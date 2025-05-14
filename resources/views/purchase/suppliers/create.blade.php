@extends('layouts.app')

@section('title', 'Ajouter un fournisseur')

@section('content')
<div>
    <form action="{{ route('purchase.suppliers.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div>
            <label for="contact_email">Email</label>
            <input type="email" name="contact_email" id="contact_email" required>
        </div>
        <div>
            <label for="contact_phone">Téléphone</label>
            <input type="text" name="contact_phone" id="contact_phone" required>
        </div>
        <button type="submit">Ajouter</button>
    </form>
</div>
@endsection
