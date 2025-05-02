
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Mon Profil</h1>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900">Information Personnelle</h2>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <div class="mt-1">
                            <input type="text" name="first_name" id="first_name" value="{{ $user->first_name ?? 'John' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <div class="mt-1">
                            <input type="text" name="last_name" id="last_name" value="{{ $user->last_name ?? 'Doe' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" value="{{ $user->email ?? 'john.doe@example.com' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <div class="mt-1">
                            <input type="text" name="phone" id="phone" value="{{ $user->phone ?? '+212 6XX-XXXXXX' }}" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <div class="mt-1">
                            <textarea id="address" name="address" rows="3" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">{{ $user->address ?? '123 Rue Example, Casablanca, Maroc' }}</textarea>
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

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h2 class="text-lg font-medium text-gray-900">Changer le mot de passe</h2>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                        <div class="mt-1">
                            <input type="password" name="current_password" id="current_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="new_password" id="new_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                        <div class="mt-1">
                            <input type="password" name="confirm_password" id="confirm_password" class="shadow-sm focus:ring-somasteel-orange focus:border-somasteel-orange block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Changer le mot de passe
                </button>
            </div>
        </div>
    </div>
@endsection
