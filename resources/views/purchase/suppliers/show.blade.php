@extends('layouts.app')

@section('title', 'Détails du fournisseur')

@section('content')

<!-- Toast Notification -->
@if(session('success') || session('error'))
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-8"
    class="fixed top-6 right-6 z-50 min-w-[180px] max-w-sm px-3 py-2 rounded-lg shadow-lg text-white font-medium flex items-center gap-2 text-sm
        {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
    <i class="fa text-base" :class="'{{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}'"></i>
    <span>{{ session('success') ?? session('error') }}</span>
</div>
@endif

<main class="mx-auto max-w-3xl px-2 sm:px-6 lg:px-8 py-8 min-h-screen bg-gradient-to-tr from-somasteel-orange/10 via-white to-gray-50 flex flex-col"
      x-data="{ editMode: false }">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-somasteel-orange flex items-center gap-2 animate-fade-in">
            <i class="fa fa-industry text-somasteel-orange"></i>
            Détails du fournisseur
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('purchase.suppliers.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-somasteel-orange/10 focus:outline-none transition-all duration-150">
                <i class="fa fa-arrow-left mr-2"></i> Retour
            </a>
            @can('update', $supplier)
            <!-- <button type="button"
               @click="editMode = true"
               x-show="!editMode"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none transition-all duration-150">
                <i class="fa fa-edit mr-2"></i> Modifier
            </button> -->
            <a href="{{ route('purchase.suppliers.edit', $supplier->id) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none transition-all duration-150">
                <i class="fa fa-edit mr-2"></i> Modifier
            </a>
            @endcan
        </div>
    </div>

    <!-- VIEW MODE -->
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 border-l-4 border-somasteel-orange animate-slide-in overflow-hidden" x-show="!editMode" x-transition>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-8">
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-user text-somasteel-orange"></i></span>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Entreprise</dt>
<dd class="mt-1 text-xl font-bold text-gray-900">{{ $supplier->company_name }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-envelope text-somasteel-orange"></i></span>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Contact person</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->contact_first_name }} {{ $supplier->contact_last_name }}</dd>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->contact_email }}</dd>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Téléphone</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->contact_phone }}</dd>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ville</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->city }}</dd>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pays</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->country }}</dd>
                </div>
            </div
            @if(!empty($supplier->address))
            <div class="flex items-start gap-3 sm:col-span-2 border-t border-dashed border-gray-200 pt-6 mt-2">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-map-marker-alt text-somasteel-orange"></i></span>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Adresse</dt>
                    <dd class="mt-1 text-lg text-gray-800">{{ $supplier->address }}</dd>
                </div>
            </div>
            @endif
            @if(!empty($supplier->notes))
            <div class="flex items-start gap-3 sm:col-span-2 border-t border-dashed border-gray-200 pt-6 mt-2">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-sticky-note text-somasteel-orange"></i></span>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Notes</dt>
                    <dd class="mt-1 text-gray-700">{{ $supplier->notes }}</dd>
                </div>
            </div>
            @endif
        </dl>
    </div>
</main>

<style>
@keyframes fade-in {
    0% { opacity: 0; transform: translateY(24px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.7s cubic-bezier(.4,0,.2,1) both; }
@keyframes slide-in {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-slide-in { animation: slide-in 0.9s cubic-bezier(.4,0,.2,1) both; }
</style>
@endsection
