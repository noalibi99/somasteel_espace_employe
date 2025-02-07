<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- No cache --}}
    
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- FAV Icon & title--}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ config('app.name', 'Espace Employé') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js', ])
    @stack('vite')
</head>
@php
    $route = Route::current();
    $routeName = $route->getName(); // Get the name of the current route
@endphp
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg bg-warning p-0 container-fluid">
            <div class="container-fluid d-flex justify-content-between p-0">
                <a class="p-0 m-0" data-bs-toggle="offcanvas" @auth href=" #offcanvasNavbar" @endauth role="button"
                    aria-controls="offcanvasNavbar">
                    <img src="{{asset("images/solasteellogowhite.png")}}" class="logoicon p-0 m-0" />
                </a>
                <a class="navbar-brand me-0 px-0" href="{{ url('/home') }}">
                    {{ config('app.name', 'Espace Employé') }}
                </a>
                <div class="d-flex align-items-center d-inline">
                    @guest
                    @if (Route::has('login'))
                            <a class="nav-link fw-bolder border-3 border-bottom border-dark me-3" href="{{ route('login') }}">{{ __('Login') }}</a>
                        {{-- @if($routeName == 'login')
                            <a class="nav-link fw-bolder border-3 border-bottom border-dark me-3" href="{{ route('login') }}">{{ __('Login') }}</a>
                            <a class="nav-link " href="{{ route('register') }}">{{ __('Registrer') }}</a>
                        @else
                            <a class="nav-link me-3" href="{{ route('login') }}">{{ __('Login') }}</a>
                            <a class="nav-link fw-bolder border-3 border-bottom border-dark" href="{{ route('register') }}">{{ __('Registrer') }}</a>
                        @endif --}}
                    @endif
                    @else
                    <a id="" class="nav-link  d-flex align-items-center badge rounded-pill text-bg-light d-inline ps-1 pe-1 fs-6"
                        href="{{route('home')}}" role="button" aria-haspopup="true" aria-expanded="false">
                        @if (Auth::user()->profile_picture)
                        <img class="user-avatar rounded-pill me-1" src="{{ route('profile.image', basename(auth()->user()->profile_picture)) }}"
                            alt="user" />
                        @else
                        <span class="user-avatar rounded-pill d-flex align-items-center justify-content-center">
                            <i class="fa fa-user"></i>
                        </span>
                        @endif
                        
                        {{ Auth::user()->nom }}
                    </a>
                    @endguest
                </div>
            </div>
            @auth
            <a class="menu-button" data-bs-toggle="offcanvas" href="#offcanvasNavbar" role="button" aria-controls="offcanvasNavbar">
                <label class="menu-label bg-warning">
                    <input type="checkbox">
                    <svg viewBox="0 0 32 32">
                        <path class="line line-top-bottom" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
                        <path class="line" d="M7 16 27 16"></path>
                    </svg>
                </label>
            </a>
            @endauth
        </nav>

        <!-- Offcanvas menu -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-2 border-warning border-bottom">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">{{ config('app.name', 'Soma Employé') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav">
                    <!-- Authentication Links -->
                    @guest
                    @if (Route::has('login'))
                    <li class="nav-item text-center">
                        <a class="nav-link active" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    {{-- <li class="nav-item text-center">
                        <a class="nav-link active" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li> --}}
                    @endif
                    @else
                    <li class="nav-item">
                        <a class="nav-link text-center"  href="{{route('home')}}" role="button">
                            {{__('Profile')}}
                        </a>
                        
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center"  href="{{route('demandes.index')}}" role="button">
                            {{__('Demandes Congé')}}
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link text-center"  href="{{ route('absence.index') }}" role="button">
                            {{__('Permission d\'absence')}}
                        </a>
                    </li> --}}
                    @if (Auth::user()->isRH() || Auth::user()->isResponsable())
                        <li class="nav-item">
                            <a class="nav-link text-center"  href="{{route('absenceDec.index')}}" role="button">
                                {{__('Déclaration des absences')}}
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->isRH())
                        <li class="nav-item">
                            <a class="nav-link text-center"  href="{{route('annuaire.index')}}" role="button">
                                {{__('Annuaire des Employés')}}
                            </a>
                        </li>
                    @endif
                    
                    <li class="nav-item">
                        <a id="logout-link" class="nav-link text-center text-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                    @endguest
                    <!-- Other navigation links go here -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li> -->
                </ul>
            </div>
        </div>

        {{-- Error Alert --}}
        <div id="dynamicErrorAlert" class="alert alert-SE alert-danger p-2" role="alert" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="errorMessage">
                @if ($errors->has('date_fin') || $errors->has('matricule') 
                || $errors->has('error') || session('error'))
                    {{ implode('<br>', $errors->get('date_fin')) }}
                    {{ implode('<br>', $errors->get('matricule')) }}
                    {{ implode('<br>', $errors->get('error')) }}
                    {{__(session('error'))}}
                @endif
            </span>
        </div>
        {{-- Success Alert --}}
        <div id="dynamicSuccessAlert" class="alert alert-SE alert-success" role="alert" style="display: none;">
            <i class="fas fa-check-to-slot me-2"></i>
            <span id="successMessage">
                @if (session('success'))
                {{__(session('success'))}}
                @endif
            </span>
        </div>
        <main class="py-2">
            @yield('content')
        </main>
    </div>
</body>

</html>
