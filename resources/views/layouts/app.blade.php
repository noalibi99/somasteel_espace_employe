
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta name="shifts-route" content="{{ route('shifts.index') }}">
    <title>SomaSteel - @yield('title')</title>
    @vite('resources/css/app.css')
    @vite(['resources/js/absenceDec.js'])
</head>
<body class="min-h-screen bg-gray-50">
<div class="flex min-h-screen">
    @include('partials.sidebar')

    <div class="lg:pl-64 w-full">
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 min-h-screen">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
@stack('vite')
</body>
</html>
