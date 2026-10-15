@extends('layouts.app')

@section('title', 'Katalog Lengkap Buket & Hadiah | MocYy')

@section('content')
<div class="bg-white border-b border-[#F4EDE0] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-[#D48C9A]">Beranda</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Katalog Buket</span>
            @if($selectedCategory)
                <span>/</span>
                <span class="text-[#D48C9A] font-bold">{{ $selectedCategory->name }}</span>
            @endif
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900">
            {{ $selectedCategory ? $selectedCategory->name : 'Katalog Buket & Hadiah Spesial' }}
        </h1>
        <p class="text-sm text-gray-500 mt-1 max-w-2xl font-light">
            {{ $selectedCategory ? $selectedCategory->description : 'Temukan berbagai pilihan buket bunga segar, snack bouquet, money bouquet, buket wisuda, dan hampers eksklusif dengan kualitas terbaik.' }}
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ mobileFilterOpen: false }">
    
    <!-- Mobile Filter & Sorting Bar (Visible only on mobile/tablet) -->
    <div class="lg:hidden mb-6 flex items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-[#F4EDE0] shadow-xs">
        <button type="button" @click="mobileFilterOpen = true"
            class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 bg-rose-50/80 hover:bg-rose-100/80 text-[#8C534A] rounded-xl text-xs font-bold transition-colors">
            <svg class="w-4 h-4 text-[#D48C9A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span>Filter & Kategori</span>
            @php
                $activeFiltersCount = (request('category') ? 1 : 0) + (request('search') ? 1 : 0) + (request('in_stock') ? 1 : 0) + (request('sort') && request('sort') !== 'latest' ? 1 : 0);
            @endphp
            @if($activeFiltersCount > 0)
                <span class="w-5 h-5 rounded-full bg-[#E07A5F] text-white text-[10px] font-bold flex items-center justify-center">
                    {{ $activeFiltersCount }}
                </span>
            @endif
        </button>

        @if($activeFiltersCount > 0)
            <a href="{{ route('products.index') }}" class="px-3.5 py-2.5 text-xs text-rose-600 hover:bg-rose-50 rounded-xl font-semibold shrink-0">
                Reset
            </a>
        @endif
    </div>

    <!-- Mobile Filter Off-Canvas Drawer (Slide-Over with Backdrop) -->
    <div x-show="mobileFilterOpen" 
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 lg:hidden bg-black/60 backdrop-blur-xs flex justify-end"
         @click.self="mobileFilterOpen = false">
        
        <div x-show="mobileFilterOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-full max-w-xs bg-white h-full flex flex-col shadow-2xl p-6 overflow-y-auto">
            
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#D48C9A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    <h3 class="font-serif text-lg font-bold text-gray-900">Filter Katalog</h3>
                </div>
                <button type="button" @click="mobileFilterOpen = false" class="p-2 text-gray-400 hover:text-gray-700 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Mobile Search Form -->
            <div class="py-4 border-b border-gray-100">
                <form method="GET" action="{{ route('products.index') }}" class="relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama buket..."
                        class="w-full pl-3.5 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    <button type="submit" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-[#D48C9A]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            <!-- Mobile Categories -->
            <div class="py-4 border-b border-gray-100 space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900">Pilih Kategori</h4>
                <div class="space-y-1">
                    <a href="{{ route('products.index', array_merge(request()->except('category', 'page'))) }}"
                        class="flex items-center justify-between py-2 px-3 rounded-xl text-xs {{ !request('category') ? 'bg-rose-50 text-[#8C534A] font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span>Semua Kategori</span>
                        <span class="text-gray-400">&rarr;</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                            class="flex items-center justify-between py-2 px-3 rounded-xl text-xs {{ request('category') === $cat->slug ? 'bg-rose-50 text-[#8C534A] font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-gray-400 text-[10px]">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Mobile Sort & Stock -->
            <form method="GET" action="{{ route('products.index') }}" class="py-4 space-y-4">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-2">Urutan Produk</h4>
                    <select name="sort" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Termurah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Termahal</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A - Z</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700 py-1">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-sm border-gray-300 text-[#D48C9A] focus:ring-[#D48C9A]">
                        <span class="font-medium">Hanya produk ready stok</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-[#D48C9A] text-white rounded-xl text-xs font-bold shadow-xs">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Desktop Filter Sidebar (Hidden on mobile) -->
        <div class="hidden lg:block lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-[#F4EDE0] shadow-xs space-y-6 sticky top-28">
                
                <!-- Search Form -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Pencarian</h3>
                    <form method="GET" action="{{ route('products.index') }}" class="relative">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama buket, bunga..."
                            class="w-full pl-3.5 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                        <button type="submit" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-[#D48C9A]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <!-- Category List -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-3">Kategori Buket</h3>
                    <div class="space-y-1 text-xs">
                        <a href="{{ route('products.index', array_merge(request()->except('category', 'page'))) }}"
                            class="flex items-center justify-between py-2 px-3 rounded-xl transition-colors {{ !request('category') ? 'bg-rose-50 text-[#8C534A] font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span>Semua Kategori</span>
                            <span class="text-gray-400">&rarr;</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                class="flex items-center justify-between py-2 px-3 rounded-xl transition-colors {{ request('category') === $cat->slug ? 'bg-rose-50 text-[#8C534A] font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-gray-400 text-[10px]">&rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4 pt-4 border-t border-gray-100">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-2">Urutkan</h3>
                        <select name="sort" onchange="this.form.submit()" class="w-full py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Termurah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Termahal</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A - Z</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded-sm border-gray-300 text-[#D48C9A] focus:ring-[#D48C9A]">
                            <span class="font-medium">Hanya produk tersedia (Ready)</span>
                        </label>
                    </div>

                    @if(request()->hasAny(['search', 'category', 'sort', 'in_stock']))
                        <div class="pt-2">
                            <a href="{{ route('products.index') }}" class="block text-center text-xs text-rose-600 hover:underline font-semibold">
                                Reset Semua Filter
                            </a>
                        </div>
                    @endif
                </form>

            </div>
        </div>

        <!-- Product Grid -->
        <div class="lg:col-span-3">
            
            @if($products->isEmpty())
                <div class="bg-white p-12 text-center rounded-3xl border border-[#F4EDE0] space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 text-[#D48C9A] flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-gray-900">Tidak ada produk ditemukan</h3>
                    <p class="text-xs text-gray-500 max-w-md mx-auto">
                        Coba gunakan kata kunci lain atau pilih kategori yang berbeda.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block px-5 py-2.5 bg-[#D48C9A] text-white text-xs font-bold rounded-full">
                        Lihat Semua Katalog
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $index => $product)
                        <div class="reveal-init delay-{{ ($index % 3 + 1) * 100 }} group bg-white rounded-3xl border border-[#F4EDE0] overflow-hidden card-hover-lift flex flex-col">
                            
                            <!-- Image Container with Zoom & Preview Button -->
                            <div class="relative aspect-4/3 bg-rose-50/50 overflow-hidden">
                                <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                                    <img src="{{ $product->main_image_url }}" 
                                         alt="{{ $product->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out">
                                </a>

                                <!-- Lightbox Zoom Button -->
                                <button type="button" 
                                        @click.prevent="$store.lightbox.open('{{ $product->main_image_url }}', '{{ addslashes($product->name) }}')"
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs text-gray-700 hover:text-[#D48C9A] hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-md z-10"
                                        title="Perbesar Foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>

                                @if($product->has_discount)
                                    <span class="absolute top-3 left-3 bg-[#E07A5F] text-white text-[11px] font-bold px-2.5 py-1 rounded-full shadow-xs">
                                        Hemat {{ $product->discount_percentage }}%
                                    </span>
                                @endif

                                @if($product->stock <= 0)
                                    <span class="absolute inset-0 bg-slate-900/60 backdrop-blur-2xs flex items-center justify-center text-white font-bold text-xs uppercase tracking-wider z-20">
                                        Stok Habis
                                    </span>
                                @endif

                                <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-xs text-[10px] font-bold px-2.5 py-1 rounded-full text-gray-700 border border-gray-100">
                                    {{ $product->category?->name }}
                                </span>

                                <span class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-xs text-[10px] font-semibold px-2 py-0.5 rounded-full text-white">
                                    ⏱️ {{ $product->lead_time_days }} hari
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">
                                        SKU: {{ $product->sku }} • Stok: {{ $product->stock }}
                                    </span>
                                    <h3 class="font-serif text-base font-bold text-gray-900 group-hover:text-[#D48C9A] transition-colors line-clamp-1">
                                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-2 font-light leading-relaxed">
                                        {{ $product->description }}
                                    </p>
                                </div>

                                <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block font-medium">Harga</span>
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-base font-bold text-[#E07A5F]">
                                                @currency($product->effective_price)
                                            </span>
                                            @if($product->has_discount)
                                                <span class="text-xs text-gray-400 line-through">
                                                    @currency($product->price)
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ route('products.show', $product->slug) }}" class="px-4 py-2 bg-rose-50 group-hover:bg-[#D48C9A] text-[#8C534A] group-hover:text-white rounded-xl text-xs font-bold transition-all shadow-xs group-hover:scale-105">
                                        Pesan &rarr;
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
