@extends('layouts.app')

@push('vite')
    @vite('resources/js/login.js')
@endpush

@section('content')
<div id="background" class="flex-column" style="background: linear-gradient(rgba(15,23,43, .7), rgba(15,23,43, .8)), url({{asset('images/somasteel.jpg')}}) center center/cover;">
        {{-- <img class="brand" src="../../images/LogoSomasteel.png" alt=""> --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="login-card pt-0">
            <a class="login">{{ __("S'identifier")}} </a>

            <div class="inputBox">
                <input id="matricule" type="text" class="@error('matricule') is-invalid @enderror" name="matricule" value="{{ old('matricule') }}" @required(true) autocomplete="matricule" autofocus>
                <span class="user px-2">{{__('Matricule')}} </span>
                @error('matricule')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ __('Matricule ou Mot the pass incorrect!') }}</strong>
                    </span>
                @enderror
                {{-- <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                <span class="user px-2">{{__('Email')}} </span>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ __('Email ou mot de pass est incorrect!') }}</strong>
                    </span>
                @enderror --}}
            </div>

            <div class="inputBox">
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                <!-- <button><i class="fa "></i></button> -->
                <span class="mdp px-2">{{__('Mot de pass')}} </span>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ 'Matricule ou Mot the pass incorrect!' }}</strong>
                    </span>
                @enderror
                <input type="checkbox" hidden id="see-password" placeholder="">
                <label for="see-password" id="see-password-label" class="fa fa-eye-slash pt-3"></label>
            </div>
            <div>
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label black-checkbox-label" for="remember">
                    {{ __('Souvenir de Moi') }}
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="location-link d-block my-2">{{__('Mot de passe oublié?')}} </a>
                @endif
            </div>
            <button type="submit" class="enter mb-3">{{ __('Entrer')}} </button>
        </div>
    </form>
    <a href="{{ route('download.app') }}" class="download-app mt-4" download>
        <span class="button__text">Télécharger</span>
        <span class="button__icon">
          <i class="fa fa-download"></i>
        </span>
      </a>      
</div>
@endsection
    