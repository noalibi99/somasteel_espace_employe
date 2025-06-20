@extends('layouts.app')

@section('title', " Demandes d'Achat")

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900"> Demandes d'Achat</h1>
        <a href="{{ route('purchase.requests.index') }}"
       class="inline-flex mt-10 px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-semibold transition">
        <i class="fas fa-arrow-left mr-2"></i>
    </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
            <strong>Succès!</strong> {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4">
            <strong>Erreur!</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Optional Search Input --}}
    <div class="relative w-64">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="fas fa-search"></i>
        </span>
        <input
            type="text"
            id="searchInput"
            placeholder="Rechercher..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
        />
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="purchaseRequestsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validateur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($Allrequests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $request->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $request->user->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full
                                            @if($request->status === 'approved') bg-green-100 text-green-800
                                            @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif
                                        ">
                                            @if($request->status === 'approved')
                                                <i class="fas fa-check"></i>
                                            @elseif($request->status === 'rejected')
                                                <i class="fas fa-times"></i>
                                            @else
                                                <i class="fas fa-hourglass-half"></i>
                                            @endif
                                            {{ ucfirst($request->status ?? 'en Attend') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->validator?->nom ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    <a href="{{ route('purchase.requests.show', $request) }}"
       class="inline-flex items-center px-1.5 py-0.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
        <i class="fa fa-eye text-xs"></i>
    </a>

    <form method="POST" action="{{ route('purchase.requests.approve', $request) }}" class="inline-block ml-1">
        @csrf
        <button type="submit"
            class="bg-green-500 text-white text-xs px-1.5 py-0.5 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500
            @if(!$currentUser->isDirector() || $request->status !== 'pending') opacity-50 cursor-not-allowed @endif"
            @disabled(!$currentUser->isDirector() || $request->status !== 'pending')>
            <i class="fa fa-check text-xs"></i>
        </button>
    </form>

    <form method="POST" action="{{ route('purchase.requests.reject', $request) }}" class="inline-block ml-1">
        @csrf
        <button type="submit"
            class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
            @if(!$currentUser->isDirector() || $request->status !== 'pending') opacity-50 cursor-not-allowed @endif"
            @disabled(!$currentUser->isDirector() || $request->status !== 'pending')>
            <i class="fa fa-xmark text-xs"></i>
        </button>
    </form>
</td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucune demande trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $Allrequests->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#purchaseRequestsTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush

@endsection
