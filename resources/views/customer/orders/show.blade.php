@extends('layouts.app')

@section('title', 'Rincian Pesanan ' . $order->order_number . ' | MocYy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- Top Bar -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-[#D48C9A]">Dashboard</a>
                <span>/</span>
                <a href="{{ route('customer.orders.index') }}" class="hover:text-[#D48C9A]">Pesanan Saya</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">{{ $order->order_number }}</span>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span>Pesanan {{ $order->order_number }}</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
        </div>

        <div class="flex items-center gap-2">
            @if($order->canUploadPaymentProof())
                <a href="{{ route('customer.orders.payment', $order->id) }}" class="px-5 py-2.5 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] text-white text-xs font-bold rounded-xl shadow-md shadow-rose-200 hover:scale-102 transition-transform">
                    Lanjutkan Pembayaran & Upload Bukti &rarr;
                </a>
            @endif

            @if(Gate::allows('cancel-order', $order))
                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan dikembalikan.')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-bold rounded-xl transition-colors">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Alert If Payment Was Rejected -->
    @if($order->isRejected())
        <div class="mb-8 p-5 bg-rose-50 border-2 border-rose-200 rounded-3xl text-rose-900 space-y-2">
            <div class="flex items-center gap-2 text-sm font-bold text-rose-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Pembayaran Anda Sebelumnya Ditolak oleh Admin</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed">
                <strong>Alasan Penolakan:</strong> "{{ $order->latestPayment?->rejection_reason ?? 'Bukti transfer tidak sesuai nominal atau mutasi belum masuk.' }}"
            </p>
            <div class="pt-2">
                <a href="{{ route('customer.orders.payment', $order->id) }}" class="inline-block px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                    Unggah Ulang Bukti Pembayaran Valid &rarr;
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Order Details & Items (8 cols) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Status Card -->
            <div class="bg-white p-6 rounded-3xl border border-[#F4EDE0] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Status Pembayaran</span>
                    <span class="inline-block text-xs font-bold px-3 py-1 rounded-full border {{ $order->payment_status_badge_class }}">
                        {{ $order->payment_status_label }}
                    </span>
                    @if($order->paid_at)
                        <span class="text-[11px] text-gray-500 block">Terverifikasi pada: {{ $order->paid_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>

                <div class="space-y-1 sm:text-right">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Status Pemrosesan Pesanan</span>
                    <span class="inline-block text-xs font-bold px-3 py-1 rounded-full border {{ $order->order_status_badge_class }}">
                        {{ $order->order_status_label }}
                    </span>
                    @if($order->tracking_number)
                        <span class="text-[11px] font-mono text-gray-700 block font-bold">No. Resi: {{ $order->tracking_number }}</span>
                    @endif
                </div>
            </div>

            <!-- Ordered Items Snapshot -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
                <h2 class="font-serif text-lg font-bold text-gray-900 pb-3 border-b border-gray-100">
                    Produk dalam Pesanan ({{ $order->items->count() }} Item)
                </h2>

                <div class="divide-y divide-gray-100 space-y-6">
                    @foreach($order->items as $item)
                        <div class="pt-6 first:pt-0 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-rose-50 shrink-0 border border-rose-100">
                                    <img src="{{ $item->product ? $item->product->main_image_url : 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=200&q=80' }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-serif text-sm font-bold text-gray-900">{{ $item->product_name }}</h3>
                                    
                                    @if($item->variant_info)
                                        <span class="text-xs text-[#8C534A] bg-rose-50 px-2 py-0.5 rounded-md inline-block">
                                            {{ $item->variant_info }}
                                        </span>
                                    @endif

                                    @if($item->greeting_card_text)
                                        <div class="text-[11px] text-gray-600 bg-amber-50/70 p-2 rounded-xl border border-amber-100 max-w-md">
                                            <strong class="text-amber-800">💌 Pesan Kartu Ucapan:</strong> "{{ $item->greeting_card_text }}"
                                        </div>
                                    @endif

                                    @if($item->custom_note)
                                        <div class="text-[11px] text-gray-500">
                                            <strong>Catatan Khusus:</strong> {{ $item->custom_note }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-xs text-gray-500 block">{{ $item->quantity }} x @currency($item->unit_price)</span>
                                <span class="text-sm font-bold text-[#E07A5F] block">@currency($item->subtotal)</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Billing Summary Breakdown -->
                <div class="pt-6 border-t border-gray-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-gray-900">@currency($order->subtotal)</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Biaya Pengiriman ({{ $order->shippingMethod?->name ?? 'Kurir' }})</span>
                        <span class="font-semibold text-gray-900">@currency($order->shipping_cost)</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex items-center justify-between text-emerald-600">
                            <span>Diskon</span>
                            <span class="font-semibold">-@currency($order->discount_amount)</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-base">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="font-bold text-xl text-[#E07A5F]">@currency($order->grand_total)</span>
                    </div>
                </div>
            </div>

            <!-- Riwayat Bukti Pembayaran yang Diunggah -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                <h2 class="font-serif text-lg font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center justify-between">
                    <span>Riwayat Unggahan Bukti Pembayaran</span>
                    <span class="text-xs text-gray-400 font-sans font-normal">Penyimpanan Privat Aman</span>
                </h2>

                @php
                    $allProofs = $order->payments->flatMap->proofs;
                @endphp

                @if($allProofs->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada bukti pembayaran yang diunggah.</p>
                @else
                    <div class="space-y-3">
                        @foreach($allProofs as $proof)
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500">
                                        @if($proof->isImage())
                                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 truncate max-w-xs">{{ $proof->original_filename }}</p>
                                        <p class="text-[11px] text-gray-400">
                                            Diunggah: {{ $proof->uploaded_at->format('d M Y, H:i') }} • {{ $proof->formatted_file_size }}
                                            @if($proof->sender_account_name)
                                                • Pengirim: <strong>{{ $proof->sender_account_name }}</strong>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('customer.proofs.download', $proof->id) }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition-colors">
                                    Lihat File
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Right: Recipient & Timeline (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Recipient Info Card -->
            <div class="bg-white p-6 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 pb-2 border-b border-gray-100">
                    Informasi Penerima & Kirim
                </h3>
                
                <div class="text-xs space-y-2 text-gray-600">
                    <div>
                        <span class="text-gray-400 block text-[11px]">Nama Penerima:</span>
                        <span class="font-bold text-gray-900">{{ $order->recipient_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[11px]">No. WhatsApp / Telepon:</span>
                        <span class="font-bold text-gray-900">{{ $order->recipient_phone }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[11px]">Alamat Tujuan:</span>
                        <p class="font-medium text-gray-800 leading-relaxed">{{ $order->shipping_address }}, {{ $order->city }} {{ $order->postal_code }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[11px]">Opsi Pengiriman:</span>
                        <span class="font-bold text-gray-900">{{ $order->shippingMethod?->name ?? 'Kurir' }}</span>
                    </div>
                    @if($order->customer_note)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <span class="text-gray-400 block text-[11px]">Catatan Anda:</span>
                            <p class="font-medium text-gray-800">{{ $order->customer_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="bg-white p-6 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900 pb-2 border-b border-gray-100">
                    Riwayat Perubahan Status
                </h3>

                <div class="space-y-4">
                    @foreach($order->statusHistories as $history)
                        <div class="relative pl-6 pb-4 border-l-2 border-rose-200 last:border-0 last:pb-0 text-xs">
                            <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-[#D48C9A]"></div>
                            <span class="text-[10px] text-gray-400 block">{{ $history->created_at->format('d M, H:i') }}</span>
                            <span class="font-bold text-gray-900 block mt-0.5">
                                {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                            </span>
                            @if($history->notes)
                                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $history->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
