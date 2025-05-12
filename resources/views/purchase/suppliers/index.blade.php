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
</main>

@endsection
