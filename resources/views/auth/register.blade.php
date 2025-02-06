@extends('layouts.app')

@section('js')
    @vite('resources/js/login.js')
@endsection

@section('content')
<div id="background" style="background: linear-gradient(rgba(15,23,43, .7), rgba(15,23,43, .8)), url({{asset('images/somasteel.jpg')}}) center center/cover;">
        {{-- <img class="brand" src="../../images/LogoSomasteel.png" alt=""> --}}
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="login-card pt-0">
            <a class="login">{{ __('Registre') }}</a>

            <div class="inputBox">
                <input id="matricule" type="number" class="@error('matricule') is-invalid @enderror" name="matricule" value="{{ old('matricule') }}" required autocomplete="matricule" autofocus>
                <span class="user px-2">{{__('Matricule')}} </span>
                @error('matricule')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ __($message) }}</strong>
                    </span>
                @enderror
            </div>

            <div class="inputBox">
                <input id="telephone" type="tel" class="@error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone') }}" required autocomplete="matricule" autofocus>
                <span class="tel px-2">{{__('N° Téléphon')}} </span>
            </div>

            <div class="inputBox">
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                <!-- <button><i class="fa "></i></button> -->
                <span class="mdp px-2">{{__('Mot de pass')}} </span>

                <input type="checkbox" hidden id="see-password" placeholder="">
                <label for="see-password" id="see-password-label" class="fa fa-eye-slash pt-3"></label>
            </div>

            <div class="inputBox">
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                <span class="mdpc px-2">{{__('Confirmer Mot de pass')}} </span>
                <!-- <button><i class="fa "></i></button> -->
                
            </div>
            <div>
                <button class="enter mb-3">{{ __('Registrer')}} </button>
            </div>
        </div>
    </form>
</div>
@endsection
    
{{-- @extends('layouts.app') --}}