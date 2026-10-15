@extends('layouts.app')

@section('title', 'Keranjang Belanja | MocYy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900">Keranjang Belanja Anda</h1>
        <p class="text-sm text-gray-500 mt-1">Periksa kembali daftar buket, variasi, dan catatan kartu ucapan sebelum melanjutkan checkout.</p>
    </div>

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-3xl border border-[#F4EDE0] p-12 text-center max-w-lg mx-auto shadow-sm space-y-4">
            <div class="w-20 h-20 mx-auto rounded-full bg-rose-50 text-[#D48C9A] flex items-center justify-center">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h2 class="font-serif text-2xl font-bold text-gray-900">Keranjang Masih Kosong</h2>
            <p class="text-sm text-gray-500">Anda belum memilih buket atau kado spesial. Jelajahi katalog kami dan temukan rangkaian terbaik.</p>
            <div class="pt-2">
                <a href="{{ route('products.index') }}" class="inline-block px-7 py-3.5 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] text-white font-bold text-xs rounded-full shadow-md shadow-rose-200 hover:scale-105 transition-all">
                    Mulai Belanja Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Items Table (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-[#F4EDE0] shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $cart->total_items }} Produk Terpilih</span>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan seluruh isi keranjang?')">
                        @csrf
                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-gray-100 space-y-6">
                    @foreach($cart->items as $item)
                        <div class="pt-6 first:pt-0 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-rose-50 shrink-0 border border-rose-100">
                                    <img src="{{ $item->product?->main_image_url }}" alt="{{ $item->product?->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-serif text-sm font-bold text-gray-900">
                                        <a href="{{ route('products.show', $item->product?->slug) }}" class="hover:text-[#D48C9A]">
                                            {{ $item->product?->name }}
                                        </a>
                                    </h3>
                                    @if($item->variant)
                                        <span class="text-xs font-medium text-rose-800 bg-rose-50 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                            {{ $item->variant->variant_name }}: {{ $item->variant->variant_value }}
                                        </span>
                                    @endif
                                    
                                    @if($item->greeting_card_text)
                                        <div class="mt-1 text-[11px] text-gray-500 bg-gray-50 p-1.5 rounded-lg border border-gray-100 max-w-xs">
                                            <strong>Kartu Ucapan:</strong> "{{ Str::limit($item->greeting_card_text, 60) }}"
                                        </div>
                                    @endif

                                    @if($item->custom_note)
                                        <div class="mt-1 text-[11px] text-gray-500 max-w-xs">
                                            <strong>Catatan:</strong> {{ $item->custom_note }}
                                        </div>
                                    @endif

                                    <span class="text-xs text-gray-500 block mt-1">
                                        @currency($item->unit_price) / unit
                                    </span>
                                </div>
                            </div>

                            <!-- Qty & Subtotal -->
                            <div class="flex items-center gap-4 self-end sm:self-center">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center border border-gray-200 rounded-xl bg-gray-50">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-rose-600 font-bold">&minus;</button>
                                    <span class="w-8 text-center text-xs font-bold text-gray-800">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-rose-600 font-bold">&plus;</button>
                                </form>

                                <div class="text-right min-w-[100px]">
                                    <span class="text-sm font-bold text-[#E07A5F] block">@currency($item->subtotal)</span>
                                </div>

                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50" title="Hapus produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary Box (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-[#F4EDE0] shadow-sm p-6 space-y-6 sticky top-28">
                <h2 class="font-serif text-lg font-bold text-gray-900 pb-3 border-b border-gray-100">
                    Ringkasan Belanja
                </h2>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Total Harga ({{ $cart->total_items }} item)</span>
                        <span class="font-semibold text-gray-900">@currency($cart->total_amount)</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="text-xs text-gray-400">Dihitung saat checkout</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Kartu Ucapan</span>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">GRATIS</span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="font-bold text-gray-900">Subtotal Sementara</span>
                        <span class="font-bold text-xl text-[#E07A5F]">@currency($cart->total_amount)</span>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <a href="{{ route('checkout.index') }}" class="w-full flex items-center justify-center gap-2 py-4 px-6 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-rose-200/60 hover:scale-102 transition-all">
                        <span>Lanjut ke Checkout</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>

                    <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-500 hover:text-[#D48C9A] font-semibold">
                        &larr; Tambah Buket Lainnya
                    </a>
                </div>

                <div class="p-3 bg-rose-50/50 rounded-xl text-[11px] text-gray-500 border border-rose-100/80">
                    🔒 Transaksi Aman dengan instruksi QRIS & DANA resmi. Stok barang dikunci aman saat pesanan dibuat.
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
