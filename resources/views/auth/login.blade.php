@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex items-center fixed justify-center pb-12 px-4 sm:px-6 lg:px-8">
    <div class="flex w-full max-w-5xl shadow-xl rounded-lg overflow-hidden">
        <!-- Left Side -->
        <div class="hidden md:flex md:w-1/2 bg-cover bg-center relative p-8 text-white" style="background: linear-gradient(rgba(15,23,43, .7), rgba(15,23,43, .8)), url({{ asset('images/workers.jpg') }}) center center/cover;">
            <div class="absolute inset-0 bg-gradient-to-b from-orange-600/60 to-orange-700/60 rounded-l-lg"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <img src="{{ asset('images/LogoSomasteel.png') }}" alt="SomaSteel Logo" class="h-10">
                </div>
                <div class="mb-12">
                    <h2 class="text-3xl font-bold mb-4">E-Procurement System</h2>
                    <p class="text-lg">Simplifiez votre processus d'approvisionnement avec notre système intégré.</p>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="w-full md:w-1/2 bg-white p-8 rounded-lg md:rounded-l-none md:rounded-r-lg">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Bienvenue !</h2>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Matricule -->
                <div>
                    <label for="matricule" class="block text-sm font-medium text-gray-700">Matricule</label>
                    <input id="matricule" type="text"
                        class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 @error('matricule') border-red-500 @enderror"
                        name="matricule" value="{{ old('matricule') }}" placeholder="ex: 12342" required autofocus>
                    @error('matricule')
                        <p class="mt-1 text-sm text-red-600">{{ __('Matricule ou Mot de passe incorrect!') }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative mt-1">
                        <input id="password" type="password"
                            class="block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 @error('password') border-red-500 @enderror"
                            name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ __('Matricule ou Mot de passe incorrect!') }}</p>
                    @enderror
                </div>

                <input type="hidden" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        {{ __('Sign In') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Vous avez oublié votre mot de passe ?<br>Merci de contacter votre administrateur ou le service IT.</p>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('download.app') }}"
                   class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Télécharger <i class="fa fa-download ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const togglePasswordBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        togglePasswordBtn.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'text';
            passwordInput.type = isVisible ? 'password' : 'text';
            eyeIcon.classList.toggle('fa-eye', isVisible);
            eyeIcon.classList.toggle('fa-eye-slash', !isVisible);
        });
    });
</script>
@endpush
