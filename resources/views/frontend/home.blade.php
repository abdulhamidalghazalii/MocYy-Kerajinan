@extends('layouts.app')

@section('title', 'MocYy | Toko Buket Bunga & Hadiah Spesial')

@section('content')
<!-- Hero Section (Pinterest & QClay Luxury E-Commerce Inspired) -->
<section class="relative overflow-hidden bg-gradient-to-b from-rose-50/70 via-[#FDFBF7] to-[#FDFBF7] py-16 sm:py-24">
    
    <!-- Ambient Floating Petals Background (Toggleable & Reduced-Motion Respectful) -->
    <div x-data x-show="$store.effects.petalsEnabled" class="absolute inset-0 pointer-events-none overflow-hidden z-10" aria-hidden="true">
        <!-- Petal 1 -->
        <svg class="petal-particle petal-particle-1 left-[10%] w-6 h-6 text-rose-300/60" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8 6 4 11 4 16a8 8 0 0016 0c0-5-4-10-8-14z" />
        </svg>
        <!-- Petal 2 -->
        <svg class="petal-particle petal-particle-2 left-[28%] w-8 h-8 text-rose-400/50" style="animation-delay: 3s;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8 6 4 11 4 16a8 8 0 0016 0c0-5-4-10-8-14z" />
        </svg>
        <!-- Petal 3 -->
        <svg class="petal-particle petal-particle-3 left-[48%] w-5 h-5 text-rose-200/70" style="animation-delay: 6s;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8 6 4 11 4 16a8 8 0 0016 0c0-5-4-10-8-14z" />
        </svg>
        <!-- Petal 4 -->
        <svg class="petal-particle petal-particle-1 left-[70%] w-7 h-7 text-rose-300/50" style="animation-delay: 2s;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8 6 4 11 4 16a8 8 0 0016 0c0-5-4-10-8-14z" />
        </svg>
        <!-- Petal 5 -->
        <svg class="petal-particle petal-particle-2 left-[88%] w-6 h-6 text-rose-400/40" style="animation-delay: 5s;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C8 6 4 11 4 16a8 8 0 0016 0c0-5-4-10-8-14z" />
        </svg>
    </div>

    <!-- Decorative Soft Glow Circles -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-rose-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-24 w-96 h-96 bg-amber-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Copy (Staggered Scroll-Reveal) -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                
                <div class="reveal-init delay-100 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-rose-200 shadow-xs text-xs font-semibold text-[#8C534A]">
                    <span class="w-2 h-2 rounded-full bg-[#E07A5F] animate-pulse"></span>
                    Koleksi Buket & Hadiah Eksklusif MocYy
                </div>

                <h1 class="reveal-init delay-200 font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-[1.15] tracking-tight">
                    Ungkapkan Rasa Kasih Lewat <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#D48C9A] via-[#E07A5F] to-[#D48C9A] italic font-normal">Rangkaian Bunga</span> & Hadiah Spesial
                </h1>

                <p class="reveal-init delay-300 text-base sm:text-lg text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                    Koleksi buket bunga segar, snack bouquet lezat, money bouquet mewah, boneka wisuda, hingga gift box hampers. Dirangkai sepenuh hati dan siap dikirim ke orang tersayang.
                </p>

                <!-- CTA Buttons with Micro-interactions -->
                <div class="reveal-init delay-400 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('products.index') }}" class="group relative overflow-hidden w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold rounded-full shadow-lg shadow-rose-200/60 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 text-sm tracking-wide text-center">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span>Jelajahi Katalog Buket</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                        <!-- Subtle Shimmer Overlay -->
                        <div class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-12 animate-shimmer pointer-events-none"></div>
                    </a>
                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Halo MocYy, saya ingin konsultasi request buket custom.') }}" target="_blank" class="w-full sm:w-auto px-7 py-4 bg-white/95 hover:bg-rose-50 text-gray-800 border border-rose-200 font-bold rounded-full transition-all text-sm flex items-center justify-center gap-2.5 shadow-xs hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                        <span>Konsultasi Custom Florist</span>
                    </a>
                </div>

                <!-- Value Highlights -->
                <div class="reveal-init delay-500 grid grid-cols-3 gap-4 pt-6 border-t border-rose-100/80">
                    <div class="group">
                        <span class="block font-serif text-xl sm:text-2xl font-bold text-gray-900 group-hover:text-[#D48C9A] transition-colors">100%</span>
                        <span class="text-xs text-gray-500 font-medium">Bunga Segar Terpilih</span>
                    </div>
                    <div class="group">
                        <span class="block font-serif text-xl sm:text-2xl font-bold text-gray-900 group-hover:text-[#D48C9A] transition-colors">Gratis</span>
                        <span class="text-xs text-gray-500 font-medium">Kartu Ucapan Custom</span>
                    </div>
                    <div class="group">
                        <span class="block font-serif text-xl sm:text-2xl font-bold text-gray-900 group-hover:text-[#D48C9A] transition-colors">QRIS / DANA</span>
                        <span class="text-xs text-gray-500 font-medium">Pembayaran Praktis</span>
                    </div>
                </div>
            </div>

            <!-- Right Showcase Card (Pinterest/QClay Floating Cards Effect) -->
            <div class="lg:col-span-5 relative reveal-init delay-200">
                <div class="relative mx-auto max-w-md">
                    
                    <!-- Ambient Glow Behind Hero Photo -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-[#D48C9A] via-[#E07A5F] to-amber-200 rounded-3xl blur-2xl animate-pulse-glow"></div>
                    
                    <!-- Main Showcase Container -->
                    <div class="relative bg-white/90 backdrop-blur-md p-3 sm:p-4 rounded-3xl shadow-2xl border border-rose-100/80 overflow-hidden group">
                        <div class="overflow-hidden rounded-2xl relative">
                            <img src="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=900&q=85" 
                                 alt="Dusty Rose Romance Bouquet" 
                                 loading="eager"
                                 class="w-full h-84 sm:h-96 object-cover rounded-2xl group-hover:scale-105 transition-transform duration-700 ease-out cursor-pointer"
                                 @click="$store.lightbox.open('https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=1400&q=90', 'Dusty Rose Romance Bouquet - Koleksi Spesial')">
                            
                            <!-- Click to Zoom Badge -->
                            <button type="button" 
                                    @click="$store.lightbox.open('https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=1400&q=90', 'Dusty Rose Romance Bouquet')" 
                                    class="absolute top-3 right-3 p-2 bg-black/40 hover:bg-black/60 text-white rounded-full backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 shadow-md"
                                    title="Klik untuk memperbesar gambar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                            </button>
                        </div>
                        
                        <!-- Floating Glassmorphism Hero Badge (Top-Left) -->
                        <div class="animate-float-gentle absolute top-6 left-6 bg-white/95 backdrop-blur-md py-1.5 px-3 rounded-full shadow-lg border border-rose-100 flex items-center gap-2">
                            <span class="text-xs">🌸</span>
                            <span class="text-[11px] font-bold text-gray-800">Fresh Flower Handcrafted</span>
                        </div>

                        <!-- Floating Glassmorphism Hero Badge (Bottom) -->
                        <div class="animate-float-reverse mt-3 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-rose-50 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#D48C9A] bg-rose-50 px-2 py-0.5 rounded-full">Best Seller #1</span>
                                    <span class="text-[11px] text-amber-500 font-bold flex items-center">★ 4.9</span>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">Dusty Rose Romance Bouquet</h3>
                                <p class="text-xs text-[#E07A5F] font-bold">Rp 285.000 <span class="line-through text-gray-400 font-normal">Rp 320.000</span></p>
                            </div>
                            <a href="{{ route('products.index', ['category' => 'bucket-bunga']) }}" class="px-4 py-2 bg-rose-50 hover:bg-[#D48C9A] text-[#8C534A] hover:text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Categories Section (Scroll Reveal & Smooth Zoom) -->
<section class="py-16 bg-white border-y border-[#F4EDE0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal-init text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-[#D48C9A]">Pilihan Lengkap</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 mt-1">Kategori Buket & Hadiah</h2>
            <p class="text-sm text-gray-500 mt-2">Temukan kado yang tepat untuk setiap momen berharga bersama orang tercinta.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
            @foreach($categories as $index => $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="reveal-init delay-{{ ($index % 5 + 1) * 100 }} group bg-[#FDFCF9] hover:bg-white rounded-2xl p-4 border border-[#F4EDE0] hover:border-rose-200 card-hover-lift flex flex-col items-center text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden mb-3 group-hover:scale-110 transition-transform duration-500 shadow-xs">
                        <img src="{{ $category->image ?: 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=200&q=80' }}" 
                             alt="{{ $category->name }}" 
                             loading="lazy"
                             class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-[#D48C9A] transition-colors line-clamp-1">
                        {{ $category->name }}
                    </h3>
                    <span class="text-[11px] text-gray-400 mt-1 group-hover:translate-x-0.5 transition-transform">Jelajahi &rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section with Interactive Lightbox & Card Hover Lift -->
<section class="py-16 bg-[#FDFBF7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal-init flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#D48C9A]">Rekomendasi Utama</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900 mt-1">Produk Buket Unggulan</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-[#D48C9A] hover:text-[#b06564] flex items-center gap-1 group">
                <span>Lihat Semua Produk</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
            @foreach($featuredProducts as $index => $product)
                <div class="reveal-init delay-{{ ($index % 4 + 1) * 100 }} group bg-white rounded-3xl border border-[#F4EDE0] overflow-hidden card-hover-lift flex flex-col">
                    
                    <!-- Product Image Container -->
                    <div class="relative overflow-hidden bg-rose-50/50 aspect-4/3">
                        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                            <img src="{{ $product->main_image_url }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out">
                        </a>

                        <!-- Quick Preview / Zoom Lightbox Button -->
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

                        <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-xs text-[10px] font-bold px-2 py-0.5 rounded-full text-gray-700 border border-gray-100">
                            {{ $product->category?->name }}
                        </span>

                        <span class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-xs text-[10px] font-semibold px-2 py-0.5 rounded-full text-white">
                            ⏱️ {{ $product->lead_time_days }} hari
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">SKU: {{ $product->sku }}</span>
                            <h3 class="font-serif text-base font-bold text-gray-900 group-hover:text-[#D48C9A] transition-colors line-clamp-1">
                                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 mt-1.5 line-clamp-2 font-light leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>

                        <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block font-medium">Mulai Dari</span>
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

                            <a href="{{ route('products.show', $product->slug) }}" class="p-2.5 rounded-xl bg-rose-50 hover:bg-[#D48C9A] text-[#8C534A] hover:text-white transition-all shadow-xs group-hover:scale-105" title="Pesan Buket Ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Step Process & Guarantees (Scroll Reveal) -->
<section class="py-16 bg-white border-t border-[#F4EDE0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal-init text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-bold uppercase tracking-widest text-[#D48C9A]">Alur Pemesanan Mudah</span>
            <h2 class="font-serif text-3xl font-bold text-gray-900 mt-1">Cara Mudah Memesan di MocYy</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="reveal-init delay-100 text-center space-y-3 p-4 rounded-2xl hover:bg-rose-50/40 transition-colors">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xl shadow-xs border border-rose-100 hover:scale-105 transition-transform">
                    1
                </div>
                <h3 class="text-base font-bold text-gray-900">Pilih Buket & Variasi</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Tentukan buket favorit, ukuran, warna kertas wrapping, serta isi pesan kartu ucapan.</p>
            </div>

            <div class="reveal-init delay-200 text-center space-y-3 p-4 rounded-2xl hover:bg-rose-50/40 transition-colors">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xl shadow-xs border border-rose-100 hover:scale-105 transition-transform">
                    2
                </div>
                <h3 class="text-base font-bold text-gray-900">Checkout & Alamat</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Masukkan detail penerima, tanggal pengiriman yang diinginkan, dan pilih kurir pengiriman.</p>
            </div>

            <div class="reveal-init delay-300 text-center space-y-3 p-4 rounded-2xl hover:bg-rose-50/40 transition-colors">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xl shadow-xs border border-rose-100 hover:scale-105 transition-transform">
                    3
                </div>
                <h3 class="text-base font-bold text-gray-900">Bayar QR & Bukti</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Scan QRIS / DANA melalui aplikasi favorit Anda, lalu unggah screenshot bukti transfer.</p>
            </div>

            <div class="reveal-init delay-400 text-center space-y-3 p-4 rounded-2xl hover:bg-emerald-50/40 transition-colors">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-xl shadow-xs border border-emerald-100 hover:scale-105 transition-transform">
                    4
                </div>
                <h3 class="text-base font-bold text-gray-900">Verifikasi & Kirim</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Admin memverifikasi dana, pesanan dirangkai rapi dengan packing aman dan dikirim ke tujuan.</p>
            </div>
        </div>
    </div>
</section>
@endsection
