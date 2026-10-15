<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autentikasi') | MocYy</title>
    <meta name="description" content="Masuk atau daftar akun di MocYy - Florist & Gift Shop terpercaya untuk buket bunga segar, money bouquet, dan hadiah spesial.">

    <!-- Fonts: Plus Jakarta Sans & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Dynamic viewport & Safe Area Support */
        .auth-viewport {
            min-height: 100vh;
            min-height: 100dvh;
            padding-top: max(0.5rem, env(safe-area-inset-top));
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
            padding-left: max(0.5rem, env(safe-area-inset-left));
            padding-right: max(0.5rem, env(safe-area-inset-right));
        }
    </style>
</head>
<body class="h-full bg-[#FAF7F2] text-[#2B2D42] font-sans antialiased selection:bg-rose-200 selection:text-rose-900 overflow-x-hidden">

    <div class="auth-viewport flex flex-col justify-center">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
