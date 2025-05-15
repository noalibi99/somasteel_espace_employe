@extends('layouts.app')

@section('title', 'Gestion des fournisseurs')

@section('content')

<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 min-h-screen bg-gray-50 flex flex-col">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-l-4 border-somasteel-orange pl-4">
            <h2 class="text-2xl font-bold text-somasteel-orange flex items-center gap-2">
                <i class="fa fa-users"></i>
                Gestion des fournisseurs
            </h2>
            @can('create', App\Models\Supplier::class)
            <a href="{{ route('purchase.suppliers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange transition">
                <i class="fas fa-circle-plus mr-2"></i>
                Nouveau fournisseur
            </a>
            @endcan
        </div>
        <!-- Search Bar -->
        <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="pl-10 pr-10 py-2 w-full border border-gray-200 rounded-full shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange text-sm" />
                <span class="absolute left-3 top-2 text-gray-400"><i class="fa fa-search"></i></span>
                @if(request('search'))
                    <a href="{{ route('purchase.suppliers.index') }}" class="absolute right-3 top-2 text-gray-400 hover:text-somasteel-orange" title="Effacer la recherche"><i class="fa fa-times-circle"></i></a>
                @endif
            </div>
        </form>
        @if (session('success') || session('error'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 2000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="fixed top-6 right-6 z-50 min-w-[180px] max-w-sm px-3 py-2 rounded-lg shadow-lg text-white font-medium flex items-center gap-2 text-sm" style="background-color: {{ session('success') ? '#34D399' : '#F56565' }}">
            <i class="fa text-base {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
            <span>{{ session('success') ?? session('error') }}</span>
        </div>
        @endif

        <!-- Fournisseurs -->
        <div class="flex flex-col mt-8">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="w-full overflow-x-auto rounded-lg shadow-md mt-6">
<table class="min-w-full divide-y divide-gray-200 bg-white">
    <thead class="bg-gray-50">
    <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
    </tr>
    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-somasteel-orange/10 even:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supplier->company_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->contact_first_name }} {{ $supplier->contact_last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->contact_email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->contact_phone }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex gap-1">
    <!-- View -->
    <a href="{{ route('purchase.suppliers.show', $supplier->id) }}"
       class="inline-flex items-center justify-center w-7 h-7 rounded-full text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition text-base focus:outline-none"
       title="Voir">
        <i class="fa fa-eye"></i>
    </a>
    <!-- Edit -->
    <a
        @can('update', $supplier)
            href="{{ route('purchase.suppliers.edit', $supplier->id) }}"
        @endcan
        class="inline-flex items-center justify-center w-7 h-7 rounded-full text-green-600 hover:bg-green-50 hover:text-green-700 transition text-base focus:outline-none @cannot('update', $supplier) opacity-50 pointer-events-none @endcannot"
        title="@can('update', $supplier) Éditer @else Non autorisé @endcan"
        @cannot('update', $supplier) tabindex="-1" aria-disabled="true" @endcannot
    >
        <i class="fa fa-pencil"></i>
    </a>
    <!-- Delete -->
    <form action="{{ route('purchase.suppliers.destroy', $supplier->id) }}"
          method="POST" onsubmit="return confirm('Supprimer ce fournisseur ?');" class="inline-block">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-red-600 hover:bg-red-50 hover:text-red-700 transition text-base focus:outline-none @cannot('delete', $supplier) opacity-50 pointer-events-none @endcannot"
                title="@can('delete', $supplier) Supprimer @else Non autorisé @endcan"
                @cannot('delete', $supplier) disabled tabindex="-1" aria-disabled="true" @endcannot>
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-folder-open text-6xl opacity-60 mb-2"></i>
                                    @if(request('search'))
                                        <span>Aucun fournisseur ne correspond à votre recherche.</span>
                                    @elseif($suppliers->isEmpty())
                                        <span>Aucun fournisseur trouvé.</span>
                                    @endif
                                    @can('create', App\Models\Supplier::class)
                                        <a href="{{ route('purchase.suppliers.create') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange transition">
                                            <i class="fas fa-circle-plus mr-2"></i> Ajouter un fournisseur
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Custom Pagination -->
                {{ $suppliers->links('vendor.pagination.somasteel') }}
            </div>
        </div>
    </div>
</div>
</main>
@endsection
