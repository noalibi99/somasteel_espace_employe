
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page non trouvée - SomaSteel</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 text-center">
<div class="flex items-center mb-8">
    <img src="{{ asset('images/logosomasteel.png') }}" alt="SomaSteel Logo" class="h-10 w-auto">
</div>

<h1 class="text-6xl font-bold text-somasteel-orange mb-2">404</h1>
<h2 class="text-2xl font-semibold mb-6">Page non trouvée</h2>
<p class="text-gray-600 max-w-md mb-8">
    La page que vous recherchez n'existe pas ou a été déplacée.
</p>

<a href="{{ route('home') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
    Retourner à l'accueil
</a>
</body>
</html>
