@extends('layouts.app')

@section('title', 'Checkout Pesanan | MocYy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
    x-data="{
        subtotal: {{ (float) $cart->total_amount }},
        shippingCost: {{ (float) ($shippingMethods->first()?->cost ?? 0) }},
        selectedShippingId: '{{ $shippingMethods->first()?->id ?? '' }}',
        selectedPaymentId: '{{ $paymentMethods->first()?->id ?? '' }}',
        useSavedAddress: {{ $addresses->isNotEmpty() ? 'true' : 'false' }},
        setShipping(id, cost) {
            this.selectedShippingId = id;
            this.shippingCost = parseFloat(cost) || 0;
        },
        get grandTotal() {
            return this.subtotal + this.shippingCost;
        },
        formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }
    }">

    <!-- Checkout Header & Step Progress -->
    <div class="mb-10 text-center max-w-xl mx-auto">
        <h1 class="font-serif text-3xl font-bold text-gray-900">Checkout & Pengiriman</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi informasi penerima hadiah dan pilih metode pembayaran favorit Anda.</p>
        
        <!-- Step Indicator -->
        <div class="flex items-center justify-center gap-3 mt-6 text-xs font-semibold">
            <span class="text-emerald-600 flex items-center gap-1">
                <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-[10px]">✓</span>
                Keranjang
            </span>
            <span class="text-gray-300">&mdash;&mdash;</span>
            <span class="text-[#D48C9A] flex items-center gap-1 font-bold">
                <span class="w-5 h-5 rounded-full bg-rose-100 flex items-center justify-center text-[10px]">2</span>
                Pengiriman & Bayar
            </span>
            <span class="text-gray-300">&mdash;&mdash;</span>
            <span class="text-gray-400 flex items-center gap-1">
                <span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[10px]">3</span>
                Instruksi QR & Upload
            </span>
        </div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Form Area (8 cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- 1. Alamat & Informasi Penerima -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xs">1</div>
                            <h2 class="font-serif text-lg font-bold text-gray-900">Alamat & Penerima Hadiah</h2>
                        </div>
                    </div>

                    @if($addresses->isNotEmpty())
                        <div class="space-y-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Pilih dari Alamat Tersimpan:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($addresses as $addr)
                                    <label class="p-4 rounded-2xl border cursor-pointer transition-all text-xs flex flex-col justify-between"
                                        :class="useSavedAddress ? 'border-[#D48C9A] bg-rose-50/40 ring-1 ring-[#D48C9A]' : 'border-gray-200 hover:border-gray-300'">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-900">{{ $addr->label }}</span>
                                                @if($addr->is_default)
                                                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Utama</span>
                                                @endif
                                            </div>
                                            <p class="font-semibold text-gray-800">{{ $addr->recipient_name }} ({{ $addr->recipient_phone }})</p>
                                            <p class="text-gray-500 font-light leading-relaxed">{{ $addr->full_address }}, {{ $addr->city }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Form Input Detail Penerima -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Penerima *</label>
                            <input type="text" name="recipient_name" required value="{{ old('recipient_name', $defaultAddress?->recipient_name ?? $user->name) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                                placeholder="Nama lengkap penerima buket">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">No. WhatsApp / Telepon Penerima *</label>
                            <input type="tel" name="recipient_phone" required value="{{ old('recipient_phone', $defaultAddress?->recipient_phone ?? $user->phone) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                                placeholder="081234567890">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Alamat Lengkap Pengiriman *</label>
                            <textarea name="full_address" rows="3" required
                                class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                                placeholder="Nama jalan, nomor rumah/gedung, RT/RW, kelurahan/kecamatan, dan patokan lokasi...">{{ old('full_address', $defaultAddress?->full_address) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kota / Kabupaten *</label>
                            <input type="text" name="city" required value="{{ old('city', $defaultAddress?->city ?? 'Jakarta Selatan') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                                placeholder="Contoh: Jakarta Selatan">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Pos (Opsional)</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $defaultAddress?->postal_code) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                                placeholder="12150">
                        </div>
                    </div>

                    @if($addresses->isEmpty())
                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600">
                                <input type="checkbox" name="save_address" value="1" checked class="rounded-sm border-gray-300 text-[#D48C9A] focus:ring-[#D48C9A]">
                                <span>Simpan alamat ini ke buku alamat saya untuk pemesanan berikutnya</span>
                            </label>
                        </div>
                    @endif
                </div>

                <!-- 2. Pilihan Kurir Pengiriman -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xs">2</div>
                        <h2 class="font-serif text-lg font-bold text-gray-900">Metode Pengiriman</h2>
                    </div>

                    <input type="hidden" name="shipping_method_id" :value="selectedShippingId">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($shippingMethods as $sm)
                            <button type="button"
                                @click="setShipping('{{ $sm->id }}', {{ (float) $sm->cost }})"
                                :class="selectedShippingId == '{{ $sm->id }}' ? 'border-[#D48C9A] bg-rose-50/50 ring-2 ring-[#D48C9A]/30' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                class="p-4 rounded-2xl border text-left transition-all flex flex-col justify-between">
                                <div>
                                    <span class="font-bold text-xs text-gray-900 block">{{ $sm->name }}</span>
                                    <p class="text-[11px] text-gray-500 mt-1 font-light leading-relaxed">{{ $sm->description }}</p>
                                    <span class="inline-block mt-2 text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-semibold">
                                        ⏱️ {{ $sm->estimated_time }}
                                    </span>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs font-bold text-[#E07A5F]">
                                        {{ $sm->cost == 0 ? 'GRATIS' : 'Rp ' . number_format($sm->cost, 0, ',', '.') }}
                                    </span>
                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                        :class="selectedShippingId == '{{ $sm->id }}' ? 'border-[#D48C9A] bg-[#D48C9A]' : 'border-gray-300'">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="selectedShippingId == '{{ $sm->id }}'"></div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Pilihan Metode Pembayaran Manual (QRIS / DANA) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#D48C9A] font-bold flex items-center justify-center text-xs">3</div>
                        <div>
                            <h2 class="font-serif text-lg font-bold text-gray-900">Metode Pembayaran (Manual Transfer QR)</h2>
                            <p class="text-xs text-gray-500">Scan QR Code dan upload bukti transfer untuk diverifikasi Admin.</p>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method_id" :value="selectedPaymentId">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($paymentMethods as $pm)
                            <button type="button"
                                @click="selectedPaymentId = '{{ $pm->id }}'"
                                :class="selectedPaymentId == '{{ $pm->id }}' ? 'border-[#D48C9A] bg-rose-50/50 ring-2 ring-[#D48C9A]/30' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                class="p-4 rounded-2xl border text-left transition-all flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-sm text-gray-900">{{ $pm->name }}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $pm->code === 'dana' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ strtoupper($pm->code) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 font-medium">a.n. {{ $pm->account_name }}</p>
                                    @if($pm->account_number)
                                        <p class="text-xs text-gray-500 font-mono">{{ $pm->account_number }}</p>
                                    @endif
                                    <p class="text-[11px] text-gray-400 font-light">Kode QR aktif & nominal unik akan tampil setelah tombol Buat Pesanan diklik.</p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-[11px] text-gray-500">Batas Waktu: {{ $pm->expiry_minutes }} Menit</span>
                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                                        :class="selectedPaymentId == '{{ $pm->id }}' ? 'border-[#D48C9A] bg-[#D48C9A]' : 'border-gray-300'">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full" x-show="selectedPaymentId == '{{ $pm->id }}'"></div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Catatan Pesanan & Kartu Ucapan Tambahan -->
                <div class="bg-white p-6 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Catatan & Pesan Khusus (Opsional)</h3>
                    <div>
                        <textarea name="customer_note" rows="2"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                            placeholder="Catatan tambahan untuk kurir atau florist (Contoh: Mohon buket jangan dibanting, sertakan pita ekstra)"></textarea>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary Sidebar (4 cols) -->
            <div class="lg:col-span-4 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6 sticky top-28">
                <h2 class="font-serif text-lg font-bold text-gray-900 pb-3 border-b border-gray-100">
                    Ringkasan Tagihan
                </h2>

                <!-- Items snapshot list -->
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1 text-xs">
                    @foreach($cart->items as $item)
                        <div class="flex items-center justify-between gap-3">
                            <div class="truncate">
                                <span class="font-bold text-gray-900 truncate block">{{ $item->product?->name }}</span>
                                <span class="text-gray-500 text-[11px]">{{ $item->quantity }}x @currency($item->unit_price)</span>
                            </div>
                            <span class="font-semibold text-gray-800 shrink-0">@currency($item->subtotal)</span>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2.5 pt-4 border-t border-gray-100 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-gray-900">@currency($cart->total_amount)</span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-gray-900" x-text="formatRupiah(shippingCost)"></span>
                    </div>

                    <div class="flex items-center justify-between text-gray-600">
                        <span>Diskon Belanja</span>
                        <span class="text-emerald-600 font-semibold">Rp 0</span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-base">
                        <span class="font-bold text-gray-900">Total Tagihan</span>
                        <span class="font-bold text-xl text-[#E07A5F]" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-rose-200/60 hover:scale-102 transition-all">
                        Buat Pesanan & Bayar Sekarang &rarr;
                    </button>
                </div>

                <div class="p-3 bg-rose-50/60 rounded-xl text-[11px] text-gray-500 border border-rose-100 space-y-1">
                    <p class="font-bold text-rose-800">📌 Penting:</p>
                    <p>Setelah menekan tombol, pesanan akan dibuat dengan status <em>Pending Payment</em> dan Anda akan diarahkan ke halaman QR pembayaran untuk scan dan upload bukti transfer.</p>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
