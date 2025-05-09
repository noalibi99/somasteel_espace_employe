@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Mes Demandes d'Achat</h2>
    
    <div class="mb-4">
        <a href="{{ route('purchase.requests.create') }}" class="btn btn-primary">
            Nouvelle Demande
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Validateur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td>{{ $request->validator?->nom ?? '-' }}</td>
                        <td>
                            <a href="{{ route('purchase.requests.show', $request) }}" class="btn btn-sm btn-info">
                                Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection