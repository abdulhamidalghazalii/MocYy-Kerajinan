@extends('layouts.app')

@section('title', $product->name . ' | MocYy')
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#D48C9A]">Beranda</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-[#D48C9A]">Katalog</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="hover:text-[#D48C9A]">{{ $product->category?->name }}</a>
        <span>/</span>
        <span class="text-gray-900 font-semibold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    @php
        $allImagesList = collect([$product->main_image_url]);
        foreach($product->images as $img) {
            $allImagesList->push($img->image_url);
        }
        $allImagesJson = json_encode($allImagesList->unique()->values()->all());
    @endphp

    <!-- Product Main Grid with Alpine.js -->
    <div x-data="{
            basePrice: {{ (float) $product->effective_price }},
            additionalPrice: 0,
            quantity: 1,
            maxStock: {{ (int) $product->stock }},
            activeImage: '{{ $product->main_image_url }}',
            allImages: {{ $allImagesJson }},
            selectedVariantId: '{{ $product->variants->first()?->id ?? '' }}',
            showGreetingCard: true,
            setVariant(id, price, stock) {
                this.selectedVariantId = id;
                this.additionalPrice = parseFloat(price) || 0;
                if (parseInt(stock) > 0) {
                    this.maxStock = Math.min({{ (int) $product->stock }}, parseInt(stock));
                } else {
                    this.maxStock = {{ (int) $product->stock }};
                }
                if (this.quantity > this.maxStock) {
                    this.quantity = Math.max(1, this.maxStock);
                }
            },
            get totalPrice() {
                return (this.basePrice + this.additionalPrice) * this.quantity;
            },
            formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            }
        }"
        x-init="
            @if($product->variants->isNotEmpty())
                setVariant('{{ $product->variants->first()->id }}', {{ (float) $product->variants->first()->additional_price }}, {{ (int) $product->variants->first()->stock }});
            @endif
        "
        class="grid grid-cols-1 lg:grid-cols-12 gap-10 bg-white p-6 sm:p-10 rounded-3xl border border-[#F4EDE0] shadow-sm">
        
        <!-- Left: Image Gallery (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Main Photo Display with Click to Lightbox Zoom -->
            <div class="relative aspect-square rounded-3xl overflow-hidden bg-rose-50/40 border border-rose-100 shadow-inner group cursor-zoom-in"
                 @click="$store.lightbox.open(activeImage, '{{ addslashes($product->name) }}', allImages)">
                
                <img :src="activeImage" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover transition-all duration-500 ease-out group-hover:scale-105">
                
                <!-- Zoom Overlay Icon -->
                <div class="absolute bottom-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2.5 rounded-full backdrop-blur-xs transition-opacity opacity-80 group-hover:opacity-100 flex items-center gap-1.5 text-xs font-semibold px-3.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                    <span>Perbesar</span>
                </div>

                @if($product->has_discount)
                    <span class="absolute top-4 left-4 bg-[#E07A5F] text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                        Hemat {{ $product->discount_percentage }}%
                    </span>
                @endif

                @if($product->stock <= 0)
                    <span class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center text-white font-bold text-sm tracking-wider uppercase z-20">
                        Stok Habis
                    </span>
                @endif
            </div>

            <!-- Thumbnail Selector -->
            @if($product->images->count() > 0)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    <button type="button" @click="activeImage = '{{ $product->main_image_url }}'"
                        :class="activeImage === '{{ $product->main_image_url }}' ? 'ring-2 ring-[#D48C9A] scale-102' : 'opacity-70 hover:opacity-100'"
                        class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 shrink-0 transition-all">
                        <img src="{{ $product->main_image_url }}" alt="Thumbnail Utama" class="w-full h-full object-cover">
                    </button>
                    @foreach($product->images as $img)
                        <button type="button" @click="activeImage = '{{ $img->image_url }}'"
                            :class="activeImage === '{{ $img->image_url }}' ? 'ring-2 ring-[#D48C9A] scale-102' : 'opacity-70 hover:opacity-100'"
                            class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 shrink-0 transition-all">
                            <img src="{{ $img->image_url }}" alt="Thumbnail Detail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Features & Guarantees -->
            <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100 text-xs text-gray-600 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-rose-500 font-bold">🌸</span>
                    <span>Waktu Pengerjaan: <strong>{{ $product->lead_time_days }} Hari Kerja</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-500 font-bold">📦</span>
                    <span>Packing Aman: Kardus Tebal + Pelindung Plastik</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-blue-500 font-bold">💳</span>
                    <span>Pembayaran: <strong>QRIS & DANA Manual</strong> (Cek Admin)</span>
                </div>
            </div>
        </div>

        <!-- Right: Information & Customization Form (7 cols) -->
        <div class="lg:col-span-7 flex flex-col justify-between">
            <form action="{{ route('cart.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" :value="selectedVariantId">

                <!-- Header info -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold text-[#8C534A] uppercase tracking-wider bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
                            {{ $product->category?->name }}
                        </span>
                        <span class="text-xs text-gray-400">SKU: {{ $product->sku }}</span>
                        <span class="text-xs font-semibold {{ $product->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            • {{ $product->stock > 0 ? "Stok Tersedia: {$product->stock}" : 'Habis' }}
                        </span>
                    </div>

                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Price Display -->
                    <div class="mt-4 flex items-baseline gap-3 p-4 bg-gradient-to-r from-rose-50/80 to-transparent rounded-2xl border border-rose-100">
                        <div class="text-2xl sm:text-3xl font-bold text-[#E07A5F]" x-text="formatRupiah(basePrice + additionalPrice)">
                            @currency($product->effective_price)
                        </div>
                        @if($product->has_discount)
                            <span class="text-sm text-gray-400 line-through">
                                @currency($product->price)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Product Description -->
                <div class="prose prose-sm text-gray-600 leading-relaxed font-light">
                    <p>{{ $product->description }}</p>
                </div>

                <!-- Variants Selection if any -->
                @if($product->variants->isNotEmpty())
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-900">
                            Pilihan Variasi / Ukuran / Nuansa
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($product->variants as $variant)
                                <button type="button"
                                    @click="setVariant('{{ $variant->id }}', {{ (float) $variant->additional_price }}, {{ (int) $variant->stock }})"
                                    :class="selectedVariantId == '{{ $variant->id }}' ? 'border-[#D48C9A] bg-rose-50 text-[#8C534A] ring-1 ring-[#D48C9A]' : 'border-gray-200 hover:border-gray-300 text-gray-700'"
                                    class="p-3 rounded-xl border text-left transition-all text-xs flex items-center justify-between">
                                    <div>
                                        <span class="font-bold block">{{ $variant->variant_name }}: {{ $variant->variant_value }}</span>
                                        @if($variant->additional_price > 0)
                                            <span class="text-[11px] text-[#E07A5F]">+@currency($variant->additional_price)</span>
                                        @else
                                            <span class="text-[11px] text-gray-400">Harga Standar</span>
                                        @endif
                                    </div>
                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                        :class="selectedVariantId == '{{ $variant->id }}' ? 'border-[#D48C9A] bg-[#D48C9A]' : 'border-gray-300'">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="selectedVariantId == '{{ $variant->id }}'"></div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Greeting Card Option -->
                @if($product->allows_greeting_card)
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#D48C9A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Kartu Ucapan Gratis
                            </label>
                            <span class="text-[11px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">FREE INCLUDED</span>
                        </div>
                        
                        <textarea name="greeting_card_text" rows="3"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                            placeholder="Tulis pesan ucapan Anda (Contoh: Happy Graduation Sarah S.Ked! Semoga berkah dan sukses selalu. From: Adit)"></textarea>
                        
                        <p class="text-[11px] text-gray-400">Kartu dicetak rapi menggunakan kertas art paper ivory dan disisipkan di buket.</p>
                    </div>
                @endif

                <!-- Custom Note Option -->
                @if($product->allows_custom_note)
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-900">
                            Catatan Khusus untuk Florist (Opsional)
                        </label>
                        <input type="text" name="custom_note"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                            placeholder="Contoh: Minta pita diganti warna gold / kirim sebelum jam 13.00 WIB">
                    </div>
                @endif

                <!-- Quantity & Action Buttons -->
                <div class="pt-6 border-t border-gray-100 space-y-4">
                    <div class="flex items-center gap-4">
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Jumlah:</label>
                        <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50">
                            <button type="button" @click="if(quantity > 1) quantity--" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-rose-600 font-bold text-lg">&minus;</button>
                            <input type="number" name="quantity" x-model="quantity" min="1" :max="maxStock" class="w-12 text-center bg-transparent border-0 font-bold text-sm text-gray-900 focus:ring-0 p-0">
                            <button type="button" @click="if(quantity < maxStock) quantity++" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-rose-600 font-bold text-lg">&plus;</button>
                        </div>
                        <span class="text-xs text-gray-400" x-text="'Maks: ' + maxStock + ' unit'"></span>
                    </div>

                    <!-- Total Estimasi -->
                    <div class="flex items-center justify-between text-sm py-2">
                        <span class="text-gray-500 font-medium">Total Harga Produk:</span>
                        <span class="font-bold text-[#E07A5F] text-lg" x-text="formatRupiah(totalPrice)"></span>
                    </div>

                    <!-- Submit Buttons -->
                    @if($product->stock > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-rose-50 hover:bg-rose-100 text-[#8C534A] border border-rose-200 font-bold text-xs tracking-wide transition-all shadow-xs flex items-center justify-center gap-2 hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span>+ Keranjang Belanja</span>
                            </button>

                            <button type="submit" name="buy_now" value="1" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs tracking-wide transition-all shadow-md shadow-rose-200 text-center hover:-translate-y-0.5">
                                Beli & Checkout Sekarang
                            </button>
                        </div>
                    @else
                        <div class="p-4 bg-gray-100 rounded-2xl text-center text-xs text-gray-500 font-semibold">
                            Maaf, stok buket ini sedang habis. Silakan hubungi kami via WhatsApp untuk PO (Pre-Order).
                        </div>
                    @endif
                </div>

            </form>
        </div>

    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-16 reveal-init">
            <h2 class="font-serif text-2xl font-bold text-gray-900 mb-6">Buket Serupa yang Mungkin Anda Sukai</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $index => $rel)
                    <div class="reveal-init delay-{{ ($index % 4 + 1) * 100 }} bg-white rounded-3xl border border-[#F4EDE0] overflow-hidden card-hover-lift p-4 flex flex-col justify-between">
                        <div>
                            <div class="aspect-square rounded-2xl overflow-hidden mb-3 bg-rose-50/50 group relative">
                                <a href="{{ route('products.show', $rel->slug) }}">
                                    <img src="{{ $rel->main_image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                                </a>
                                <button type="button" 
                                        @click="$store.lightbox.open('{{ $rel->main_image_url }}', '{{ addslashes($rel->name) }}')"
                                        class="absolute top-2 right-2 p-1.5 rounded-full bg-white/90 text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity shadow-xs"
                                        title="Perbesar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </div>
                            <h3 class="font-serif text-sm font-bold text-gray-900 line-clamp-1 hover:text-[#D48C9A]">
                                <a href="{{ route('products.show', $rel->slug) }}">{{ $rel->name }}</a>
                            </h3>
                            <span class="text-xs font-bold text-[#E07A5F] block mt-1">@currency($rel->effective_price)</span>
                        </div>
                        <a href="{{ route('products.show', $rel->slug) }}" class="mt-3 block text-center py-2 bg-rose-50 text-[#8C534A] rounded-xl text-xs font-bold hover:bg-[#D48C9A] hover:text-white transition-colors">
                            Lihat Buket &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
