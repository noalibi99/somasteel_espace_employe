@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demandes d'Achat en Attente de Validation</h2>
    
    @if($pendingRequests->isEmpty())
        <div class="alert alert-info">
            Aucune demande en attente de validation.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Demandeur</th>
                            <th>Date</th>
                            <th>Nb. Articles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->user->nom }}  {{ $request->user->prénom }}</td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $request->lines->count() }}</td>
                            <td>
                                <a href="{{ route('purchase.requests.show', $request) }}" 
                                   class="btn btn-sm btn-primary">
                                    Voir Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{ $pendingRequests->links() }}
            </div>
        </div>
    @endif
</div>
@endsection