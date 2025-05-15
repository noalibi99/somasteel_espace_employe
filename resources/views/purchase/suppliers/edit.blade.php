@extends('layouts.app')

@section('title', 'Modifier le fournisseur')

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

<main class="mx-auto max-w-3xl px-2 sm:px-6 lg:px-8 py-8 min-h-screen bg-gradient-to-tr from-somasteel-orange/10 via-white to-gray-50 flex flex-col">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-somasteel-orange flex items-center gap-2 animate-fade-in">
            <i class="fa fa-edit text-somasteel-orange"></i>
            Modifier le fournisseur
        </h1>
        <a href="{{ old('return_url', url()->previous()) }}"
   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-somasteel-orange/10 focus:outline-none transition-all duration-150">
    <i class="fa fa-arrow-left mr-2"></i> Retour
</a>
<input type="hidden" name="return_url" value="{{ old('return_url', request()->get('return_url', url()->previous())) }}">
    </div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 border-l-4 border-somasteel-orange animate-slide-in mt-0 overflow-hidden">
        <form method="POST" action="{{ route('purchase.suppliers.update', $supplier->id) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-8">
            @csrf
            @method('PUT')
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-user text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="company_name" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Entreprise</label>
                    <input type="text" name="company_name" id="company_name" required value="{{ old('company_name', $supplier->company_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg font-bold text-gray-900">
                    @error('company_name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-user text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="contact_first_name" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Prénom du contact</label>
                    <input type="text" name="contact_first_name" id="contact_first_name" required value="{{ old('contact_first_name', $supplier->contact_first_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-900">
                    @error('contact_first_name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-user text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="contact_last_name" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Nom du contact</label>
                    <input type="text" name="contact_last_name" id="contact_last_name" required value="{{ old('contact_last_name', $supplier->contact_last_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-900">
                    @error('contact_last_name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-envelope text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="contact_email" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $supplier->contact_email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-800">
                    @error('contact_email')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-phone text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="contact_phone" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Téléphone</label>
                    <input type="text" name="contact_phone" id="contact_phone" inputmode="numeric" minlength="10" value="{{ old('contact_phone', $supplier->contact_phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-800">
                    @error('contact_phone')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-city text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="city" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ville</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $supplier->city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-800">
                    @error('city')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 mt-1"><i class="fa fa-flag text-somasteel-orange"></i></span>
                <div class="w-full">
                    <label for="country" class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pays</label>
                    <input type="text" name="country" id="country" value="{{ old('country', $supplier->country) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-somasteel-orange focus:ring focus:ring-somasteel-orange/30 text-lg text-gray-800">
                    @error('country')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 sm:col-span-2 mt-8">
                <a href="{{ route('purchase.suppliers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-all duration-150">
                    <i class="fa fa-times mr-2"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none transition-all duration-150">
                    <i class="fa fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
