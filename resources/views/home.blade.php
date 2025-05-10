
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
            <strong>Succès!</strong> {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4">
            <strong>Erreur!</strong> {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Mon Profil</h1>
        </div>
        <form action="{{ route('home.update') }}"  method="POST">
            @method('PUT')
            @csrf
            <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900">Information Personnelle</h2>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <div class="mt-1">
                            <input type="text" name="first_name" id="first_name" value="{{ $userInfo->prénom ?? 'None' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <div class="mt-1">
                            <input type="text" name="last_name" id="last_name" value="{{ $userInfo->nom ?? 'None' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" value="{{ $userInfo->email ?? 'email@somasteel.ma' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('email')
                        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="matricule" class="block text-sm font-medium text-gray-700">Matricule</label>
                        <div class="mt-1">
                            <input type="text" name="matricule" id="matricule" value="{{ $userInfo->matricule ?? '9999' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                        <div class="mt-1">
                            <input type="text" name="service" id="service" value="{{ $userInfo->service ?? 'None' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="fonction" class="block text-sm font-medium text-gray-700">Fonction</label>
                        <div class="mt-1">
                            <input type="text" name="fonction" id="fonction" value="{{ $userInfo->fonction ?? 'None' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                    </div>

                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Enregistrer
                </button>
            </div>
        </div>
        </form>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900">Changer le mot de passe</h2>
            </div>
            <form action="{{ route('home.updatePassword') }}" method="POST">
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        @method('PUT')
                        @csrf
                    <div class="sm:col-span-6">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                        <div class="mt-1">
                            <input type="password" name="current_password" id="current_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('current_password')
                        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sm:col-span-6">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="new_password" id="new_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    @error('new_password')
                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                    @enderror
                    <div class="sm:col-span-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="new_password_confirmation" id="confirm_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('confirm_password')
                        <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Changer le mot de passe
                </button>
            </div>
            </form>
        </div>
    </div>
@endsection
