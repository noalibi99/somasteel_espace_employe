@extends('layouts.app')

@section('title', 'Gestion des fournisseurs')

@section('content')

<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 min-h-screen bg-white flex flex-col shadow-xl">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Fournisseurs</h1>
            <button type="button" onclick="openLeaveModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fa fa-plus mr-2"></i>
                Nouveau fournisseur
            </button>
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

        <!-- Fournisseurs -->
        <div class="flex flex-col mt-8">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $supplier->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $supplier->contact_email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $supplier->contact_phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex gap-1">
                                    <!-- View -->
                                    <a href="{{ route('purchase.suppliers.show', $supplier->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-blue-100 text-blue-600"
                                       title="Voir">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <!-- Edit -->
                                    <a 
                                        @if($currentUser->isRH() || $currentUser->isAdmin())
                                            href="{{ route('purchase.suppliers.edit', $supplier->id) }}"
                                        @endif
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                            {{ ($currentUser->isRH() || $currentUser->isAdmin()) ? 'bg-gray-100 hover:bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}"
                                        title="{{ ($currentUser->isRH() || $currentUser->isAdmin()) ? 'Éditer' : 'Non autorisé' }}"
                                    >
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- Delete -->
                                    <form action="{{ route('purchase.suppliers.destroy', $supplier->id) }}"
                                          method="POST" onsubmit="return confirm('Supprimer ce fournisseur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ !$currentUser->isRH() && !$currentUser->isAdmin() ? 'cursor-not-allowed disabled text-gray-400 bg-gray-200' : 'text-red-600 hover:bg-red-100 bg-gray-100' }}"
                                                title="Supprimer"
                                                @disabled(!$currentUser->isRH() && !$currentUser->isAdmin())>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun fournisseur trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>

@endsection
