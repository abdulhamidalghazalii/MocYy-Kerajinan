<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MocYy | Toko Buket Bunga & Hadiah Spesial')</title>
    <meta name="description" content="@yield('meta_description', 'Toko buket bunga segar, snack bouquet, money bouquet, buket wisuda, hampers, dan kado hadiah custom MocYy dengan pembayaran QRIS & DANA terverifikasi.')">

    <!-- Performance & Preconnect Hints -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//images.unsplash.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-[#FDFBF7] text-[#2B2D42] font-sans antialiased selection:bg-rose-200 selection:text-rose-900">
    
    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-[#D48C9A] via-[#E07A5F] to-[#D48C9A] text-white text-xs sm:text-sm py-2 px-4 text-center font-medium tracking-wide shadow-xs">
        <span class="inline-flex items-center gap-1.5">
            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
            ✨ Pesan Buket & Hadiah Eksklusif MocYy Hari Ini! Pembayaran Praktis via QRIS (DANA, GoPay, OVO, ShopeePay & Bank).
        </span>
    </div>

    <!-- Main Navigation Header -->
    <header x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#F4EDE0] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo (MocYy Elegant Typography Logo) -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#D48C9A] to-[#E07A5F] flex items-center justify-center text-white shadow-md shadow-rose-200 group-hover:scale-105 transition-transform duration-300">
                            <span class="font-serif text-xl font-bold">M</span>
                        </div>
                        <div>
                            <span class="font-serif text-2xl font-bold text-[#2B2D42] tracking-tight group-hover:text-[#D48C9A] transition-colors">MocYy</span>
                            <span class="block text-[10px] tracking-widest uppercase font-semibold text-[#8C534A]">Florist & Gift Shop</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-[#D48C9A]' : 'text-gray-600 hover:text-[#D48C9A]' }} transition-colors">Beranda</a>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold {{ request()->routeIs('products.*') ? 'text-[#D48C9A]' : 'text-gray-600 hover:text-[#D48C9A]' }} transition-colors">Katalog Buket</a>
                    <a href="{{ route('products.index', ['category' => 'bucket-wisuda']) }}" class="text-sm font-semibold text-gray-600 hover:text-[#D48C9A] transition-colors">Wisuda</a>
                    <a href="{{ route('products.index', ['category' => 'money-bouquet']) }}" class="text-sm font-semibold text-gray-600 hover:text-[#D48C9A] transition-colors">Money Bouquet</a>
                    <a href="{{ route('products.index', ['category' => 'hampers-dan-gift-box']) }}" class="text-sm font-semibold text-gray-600 hover:text-[#D48C9A] transition-colors">Hampers</a>
                </nav>

                <!-- Actions: Cart & Auth -->
                <div class="flex items-center gap-4">
                    <!-- Search Icon / Quick Link -->
                    <a href="{{ route('products.index') }}" class="p-2 text-gray-500 hover:text-[#D48C9A] hover:bg-rose-50 rounded-full transition-colors" title="Cari Buket">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </a>

                    <!-- Shopping Cart Icon with Badge -->
                    @php
                        $cartCount = 0;
                        if (auth()->check()) {
                            $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
                            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                        } else {
                            $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
                            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 bg-[#FDF2F4] text-[#D48C9A] hover:bg-[#FCE7EB] rounded-full transition-all duration-200" title="Keranjang Belanja">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-[#E07A5F] text-white text-[11px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-xs animate-bounce">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Authentication / User Menu -->
                    @auth
                        <div class="relative" @click.outside="userDropdownOpen = false">
                            <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 py-1.5 px-3 rounded-full border border-rose-100 hover:border-rose-200 bg-white hover:bg-rose-50/50 transition-all text-left">
                                <div class="w-8 h-8 rounded-full bg-[#E8C6C5] text-[#8C534A] font-bold flex items-center justify-center text-xs">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:inline-block text-xs font-semibold text-gray-700 max-w-[100px] truncate">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userDropdownOpen" x-transition.origin.top.right class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 divide-y divide-gray-50" style="display: none;">
                                <div class="px-4 py-2.5">
                                    <p class="text-xs text-gray-500">Masuk sebagai</p>
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <span class="inline-block px-2 py-0.5 mt-1 text-[10px] font-bold rounded-full {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Customer' }}
                                    </span>
                                </div>

                                <div class="py-1">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-purple-700 hover:bg-purple-50 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                            Dashboard Admin
                                        </a>
                                        <a href="{{ route('admin.payments.verification') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-purple-700 hover:bg-purple-50 font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Verifikasi Pembayaran
                                        </a>
                                    @endif

                                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Dashboard Saya
                                    </a>
                                    <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Riwayat Pesanan
                                    </a>
                                    <a href="{{ route('customer.profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                        Profil & Alamat
                                    </a>
                                </div>

                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Keluar (Logout)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="hidden sm:flex items-center gap-2.5">
                            <a href="{{ route('login') }}" class="text-xs font-semibold px-4 py-2 text-gray-700 hover:text-[#D48C9A] transition-colors">Masuk</a>
                            <a href="{{ route('register') }}" class="text-xs font-semibold px-4 py-2 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] text-white rounded-full hover:shadow-md hover:shadow-rose-200 transition-all">Daftar</a>
                        </div>
                    @endauth

                    <!-- Mobile Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-600 hover:text-[#D48C9A] rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white border-b border-rose-100 px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">Beranda</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">Katalog Lengkap</a>
            <a href="{{ route('products.index', ['category' => 'bucket-wisuda']) }}" class="block px-3 py-2 text-base font-medium rounded-lg text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">Buket Wisuda</a>
            <a href="{{ route('products.index', ['category' => 'money-bouquet']) }}" class="block px-3 py-2 text-base font-medium rounded-lg text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">Money Bouquet</a>
            <a href="{{ route('products.index', ['category' => 'hampers-dan-gift-box']) }}" class="block px-3 py-2 text-base font-medium rounded-lg text-gray-700 hover:bg-rose-50 hover:text-[#D48C9A]">Hampers Gift Box</a>
            
            @guest
                <div class="pt-4 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 text-sm font-semibold border border-rose-200 text-rose-700 rounded-xl">Masuk</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 text-sm font-semibold bg-[#D48C9A] text-white rounded-xl shadow-xs">Daftar</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Global Flash Notification Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl shadow-xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-sky-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm font-medium">{{ session('info') }}</span>
                </div>
                <button @click="show = false" class="text-sky-500 hover:text-sky-700">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-1 pb-24 md:pb-0">
        @yield('content')
    </main>

    <!-- Floating WhatsApp Action Button (Responsive positioning) -->
    <a href="{{ \App\Models\StoreSetting::whatsappUrl('Halo MocYy, saya ingin konsultasi pemesanan buket custom.') }}" target="_blank" rel="noopener noreferrer" class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-40 sm:z-50 hidden sm:flex items-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white py-3 px-5 rounded-full shadow-lg shadow-emerald-500/30 hover:scale-105 transition-all duration-300 font-semibold text-sm">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
        <span>Chat WhatsApp</span>
    </a>

    <!-- Mobile Quick-Access App Bar (Fixed Bottom on Mobile) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-rose-100 shadow-[0_-4px_25px_rgba(212,140,154,0.15)] pb-safe transition-all" aria-label="Mobile Navigation">
        <div class="grid grid-cols-5 items-center justify-around h-16 px-1 text-center">
            
            <!-- 1. Home -->
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('home') ? 'text-[#D48C9A]' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            <!-- 2. Katalog -->
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('products.*') ? 'text-[#D48C9A]' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="text-[10px] font-semibold">Katalog</span>
            </a>

            <!-- 3. Keranjang (Center Accent with live count badge) -->
            <a href="{{ route('cart.index') }}" class="relative flex flex-col items-center justify-center py-1 {{ request()->routeIs('cart.*') ? 'text-[#D48C9A]' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                <div class="relative">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1.5 -right-2 bg-[#E07A5F] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-xs">
                            {{ $cartCount }}
                        </span>
                    @endif
                </div>
                <span class="text-[10px] font-semibold">Keranjang</span>
            </a>

            <!-- 4. Akun / Pesanan -->
            @auth
                <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('customer.*') ? 'text-[#D48C9A]' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-[10px] font-semibold">Akun Saya</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('login') ? 'text-[#D48C9A]' : 'text-gray-500 hover:text-gray-900' }} transition-colors">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <span class="text-[10px] font-semibold">Masuk</span>
                </a>
            @endauth

            <!-- 5. WhatsApp Support -->
            <a href="{{ \App\Models\StoreSetting::whatsappUrl('Halo MocYy, saya ingin bertanya tentang buket & kado.') }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center justify-center py-1 text-emerald-600 hover:text-emerald-700 transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                <span class="text-[10px] font-semibold text-emerald-700">Chat WA</span>
            </a>

        </div>
    </nav>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#F4EDE0] mt-20 pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                
                <!-- Col 1: Store Intro -->
                <div class="md:col-span-1 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#D48C9A] to-[#E07A5F] flex items-center justify-center text-white font-serif font-bold text-lg">
                            M
                        </div>
                        <span class="font-serif text-2xl font-bold text-[#2B2D42]">MocYy</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Handcrafted bouquet & hampers hadiah eksklusif untuk wisuda, ulang tahun, anniversary, dan momen berharga Anda. Dirangkai sepenuh hati dengan bunga berkualitas.
                    </p>
                    <div class="pt-2 text-xs text-gray-500 space-y-1">
                        <div>📍 {{ \App\Models\StoreSetting::get('store_address', config('store.address')) }}</div>
                        <div>📞 <a href="{{ \App\Models\StoreSetting::whatsappUrl() }}" target="_blank" class="hover:text-[#D48C9A] font-semibold">{{ \App\Models\StoreSetting::get('store_phone', config('store.phone')) }}</a></div>
                    </div>
                </div>

                <!-- Col 2: Kategori Populer -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-4">Kategori Populer</h3>
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li><a href="{{ route('products.index', ['category' => 'bucket-bunga']) }}" class="hover:text-[#D48C9A] transition-colors">Bucket Bunga Segar</a></li>
                        <li><a href="{{ route('products.index', ['category' => 'bucket-snack']) }}" class="hover:text-[#D48C9A] transition-colors">Bucket Snack & Cokelat</a></li>
                        <li><a href="{{ route('products.index', ['category' => 'money-bouquet']) }}" class="hover:text-[#D48C9A] transition-colors">Money Bouquet Elegan</a></li>
                        <li><a href="{{ route('products.index', ['category' => 'bucket-wisuda']) }}" class="hover:text-[#D48C9A] transition-colors">Buket Wisuda & Kelulusan</a></li>
                        <li><a href="{{ route('products.index', ['category' => 'hampers-dan-gift-box']) }}" class="hover:text-[#D48C9A] transition-colors">Hampers & Exclusive Gift Box</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan & Informasi -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-4">Layanan Pelanggan</h3>
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li><a href="{{ route('cart.index') }}" class="hover:text-[#D48C9A] transition-colors">Keranjang Belanja</a></li>
                        <li><a href="{{ route('customer.orders.index') }}" class="hover:text-[#D48C9A] transition-colors">Cek Status Pesanan</a></li>
                        <li><a href="{{ route('customer.dashboard') }}" class="hover:text-[#D48C9A] transition-colors">Upload Bukti Pembayaran</a></li>
                        <li><a href="{{ \App\Models\StoreSetting::whatsappUrl('Halo MocYy, saya ingin konsultasi request custom.') }}" target="_blank" class="hover:text-[#D48C9A] transition-colors">Konsultasi Request Custom</a></li>
                    </ul>
                </div>

                <!-- Col 4: Metode Pembayaran Aman -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 mb-4">Metode Pembayaran</h3>
                    <p class="text-xs text-gray-500 mb-3">Mendukung pembayaran manual mudah & aman via scan QR:</p>
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600">DANA</div>
                        <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600">QRIS</div>
                        <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700">Bank Transfer</div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-4 leading-normal">
                        🛡️ Bukti transfer diverifikasi langsung oleh Admin toko sebelum pesanan dirangkai dan dikirim.
                    </p>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400">
                <p>&copy; {{ date('Y') }} MocYy. Seluruh Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4 mt-2 sm:mt-0">
                    <!-- Petals Effect Toggle -->
                    <button type="button" @click="$store.effects.togglePetals()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors text-[11px] font-semibold">
                        <span>🌸 Efek Kelopak:</span>
                        <span x-text="$store.effects.petalsEnabled ? 'Aktif' : 'Nonaktif'"></span>
                    </button>
                    <span class="font-medium text-gray-500">Dibuat dengan Laravel 12 & PostgreSQL</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Image Lightbox Modal -->
    <div x-data x-show="$store.lightbox.isOpen" x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/90 backdrop-blur-md"
         @click.self="$store.lightbox.close()">
        
        <!-- Close Button -->
        <button @click="$store.lightbox.close()" class="absolute top-5 right-5 text-white/80 hover:text-white p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-all focus:outline-none z-10" title="Tutup (ESC)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Navigation Buttons -->
        <template x-if="$store.lightbox.images.length > 1">
            <button @click="$store.lightbox.prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition-all focus:outline-none z-10" title="Foto Sebelumnya">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
        </template>
        <template x-if="$store.lightbox.images.length > 1">
            <button @click="$store.lightbox.next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition-all focus:outline-none z-10" title="Foto Berikutnya">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </template>

        <!-- Image Container -->
        <div class="relative max-w-4xl max-h-[85vh] flex flex-col items-center">
            <img :src="$store.lightbox.currentImage" :alt="$store.lightbox.title" 
                 class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl transition-transform duration-300">
            <p x-show="$store.lightbox.title" x-text="$store.lightbox.title" class="text-white/90 text-sm font-medium mt-3 text-center"></p>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
