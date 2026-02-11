<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CMMS - Connexion</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <!-- Logos -->
            <div class="flex items-center justify-center gap-8 mb-8">
                <!-- Logo Mibtech -->
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/mibtech-logo.png') }}" alt="Mibtech Logo" class="h-20 w-auto object-contain">
                </div>
                
            </div>

            <!-- Titre -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">MibTech CMMS</h1>
                <p class="text-gray-600 mt-2">Gestion de Maintenance</p>
            </div>

            <!-- Formulaire de connexion -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl border border-gray-200 overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-500">
                © {{ date('Y') }} MibTech - Tous droits réservés
            </div>
        </div>
    </body>
</html>
