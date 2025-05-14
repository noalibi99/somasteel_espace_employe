@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-8">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-6">Demande d'Achat <span class="text-somasteel-orange">#{{ $request->id }}</span></h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="space-y-3">
            <p class="text-gray-700"><span class="font-semibold">Demandeur:</span> {{ $request->user->nom }} {{ $request->user->prénom }}</p>
            <p class="text-gray-700"><span class="font-semibold">Date de création:</span> {{ $request->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="space-y-3">
            <p>
                <span class="font-semibold">Statut:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                    @if(strtolower($request->status) === 'approved') bg-green-100 text-green-800
                    @elseif(strtolower($request->status) === 'rejected') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800 @endif
                ">
                    @if(strtolower($request->status) === 'approved')
                        <i class="fas fa-check mr-2"></i>
                    @elseif(strtolower($request->status) === 'rejected')
                        <i class="fas fa-times mr-2"></i>
                    @else
                        <i class="fas fa-hourglass-half mr-2"></i>
                    @endif
                    {{ ucfirst($request->status) }}
                </span>
            </p>
            @if($request->validator)
                <p class="text-gray-700"><span class="font-semibold">Validateur:</span> {{ $request->validator->nom }}</p>
                <p class="text-gray-700"><span class="font-semibold">Date validation:</span> 
                    {{ $request->validated_at ? $request->validated_at->format('d/m/Y H:i') : 'Non validée' }}
                </p>
            @endif
        </div>
    </div>

    <section class="mb-10">
        <h3 class="text-xl font-semibold text-gray-900 mb-3 border-b border-gray-200 pb-2">Description</h3>
        <p class="text-gray-700 whitespace-pre-line">{{ $request->description }}</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Articles Demandés</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 shadow-sm rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantité</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($request->lines as $line)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->article->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if(auth()->user()->isDirector() && strtolower($request->status) === 'pending')
        <div class="flex space-x-4 mt-8">
            <form method="POST" action="{{ route('purchase.requests.approve', $request) }}">
                @csrf
                <button type="submit" 
                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                    <i class="fas fa-check mr-2"></i> Approuver
                </button>
            </form>
            <form method="POST" action="{{ route('purchase.requests.reject', $request) }}">
                @csrf
                <button type="submit" 
                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    <i class="fas fa-times mr-2"></i> Rejeter
                </button>
            </form>
        </div>
    @endif

    <a href="{{ route('purchase.requests.index') }}" 
       class="inline-block mt-10 px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-semibold transition">
        <i class="fas fa-arrow-left mr-2"></i> </a>
</div>
@endsection