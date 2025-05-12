@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demande d'Achat #{{ $request->id }}</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Demandeur:</strong> {{ $request->user->nom }}  {{ $request->user->prénom }}</p>
                    <p><strong>Date:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Statut:</strong> 
                        <span class="">
                            {{ $request->status }}
                        </span>
                    </p>
                    @if($request->validator)
                        <p><strong>Validateur:</strong> {{ $request->validator->name }}</p>
                        <p><strong>Date validation:</strong> 
                        {{ $request->validated_at ? $request->validated_at->format('d/m/Y H:i') : 'Non validée' }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="mt-3">
                <h5>Description:</h5>
                <p>{{ $request->description }}</p>
            </div>
        </div>
    </div>
    
    <h4>Articles Demandés</h4>
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantité</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($request->lines as $line)
    <tr>
        <td>{{ $line->article->description }}</td>  <!-- Accès à la description de l'article via la relation -->
        <td>{{ $line->quantity }}</td>
    </tr>
    @endforeach
</tbody>
            </table>
        </div>
    </div>
    
    @if(auth()->user()->isDirector() && $request->status === 'pending')
    <div class="mt-4">
        <form method="POST" action="{{ route('purchase.requests.approve', $request) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">Approuver</button>
        </form>
        
        <form method="POST" action="{{ route('purchase.requests.reject', $request) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger">Rejeter</button>
        </form>
    </div>
    @endif
    
    <a href="{{ route('purchase.requests.index') }}" class="btn btn-secondary mt-3">Retour</a>
</div>
@endsection