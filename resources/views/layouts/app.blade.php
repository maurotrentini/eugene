<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Eugene</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <header class="bg-blue-500 py-4">
        <div class="container mx-auto px-4">
            <a href="/" class="text-white text-2xl font-semibold">{{ config('app.name', 'Eugene') }}</a>
        </div>
    </header>

    <main class="mt-8 mb-8">
        @yield('content')
    </main>

    <!-- Scripts -->
    @vite('resources/js/app.js')
    @livewireScripts
</body>
</html>
