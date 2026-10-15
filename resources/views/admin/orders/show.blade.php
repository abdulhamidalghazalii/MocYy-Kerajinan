@extends('layouts.admin')

@section('title', 'Kelola Pesanan ' . $order->order_number)
@section('page_title', 'Kelola Pesanan: ' . $order->order_number)

@section('content')
<div class="space-y-6">
    
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Daftar Pesanan
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $order->payment_status_badge_class }}">
                Bayar: {{ $order->payment_status_label }}
            </span>
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $order->order_status_badge_class }}">
                Pesanan: {{ $order->order_status_label }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Items & Delivery Info (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Items Box -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h2 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100">
                    Item Buket yang Dipesan ({{ $order->items->count() }})
                </h2>

                <div class="divide-y divide-slate-100 space-y-4">
                    @foreach($order->items as $item)
                        <div class="pt-4 first:pt-0 flex items-start justify-between gap-4 text-xs">
                            <div class="flex items-start gap-3">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                    <img src="{{ $item->product ? $item->product->main_image_url : 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=200&q=80' }}" alt="Produk" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</h4>
                                    @if($item->variant_info)
                                        <span class="text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md inline-block">
                                            {{ $item->variant_info }}
                                        </span>
                                    @endif
                                    
                                    @if($item->greeting_card_text)
                                        <div class="p-2.5 bg-amber-50/70 border border-amber-200 rounded-xl text-amber-900 max-w-sm mt-1">
                                            <strong class="block text-[11px] text-amber-800">💌 Pesan Kartu Ucapan:</strong>
                                            <p class="italic text-[11px] mt-0.5 font-serif leading-relaxed">"{{ $item->greeting_card_text }}"</p>
                                        </div>
                                    @endif

                                    @if($item->custom_note)
                                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 max-w-sm mt-1 text-[11px]">
                                            <strong>Catatan Khusus:</strong> {{ $item->custom_note }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-slate-400 block">{{ $item->quantity }} x @currency($item->unit_price)</span>
                                <span class="font-bold text-slate-900 text-sm">@currency($item->subtotal)</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Produk:</span>
                        <span class="font-bold text-slate-900">@currency($order->subtotal)</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim ({{ $order->shippingMethod?->name }}):</span>
                        <span class="font-bold text-slate-900">@currency($order->shipping_cost)</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex justify-between text-sm">
                        <span class="font-bold text-slate-900">Total Akhir Pesanan:</span>
                        <span class="font-bold text-[#E07A5F] text-base">@currency($order->grand_total)</span>
                    </div>
                </div>
            </div>

            <!-- Recipient & Customer Details -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3 text-xs">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">
                    Informasi Pengiriman & Kontak
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Customer Pemesan:</span>
                        <span class="font-bold text-slate-900">{{ $order->user?->name }} ({{ $order->user?->email }})</span>
                        <span class="block text-slate-500">{{ $order->user?->phone }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Penerima Hadiah:</span>
                        <span class="font-bold text-slate-900">{{ $order->recipient_name }}</span>
                        <span class="block text-slate-500">{{ $order->recipient_phone }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block text-[11px]">Alamat Tujuan:</span>
                        <p class="font-medium text-slate-800 leading-relaxed">{{ $order->shipping_address }}, {{ $order->city }} {{ $order->postal_code }}</p>
                    </div>
                    @if($order->customer_note)
                        <div class="sm:col-span-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-400 block text-[11px]">Catatan Pembeli:</span>
                            <p class="text-slate-700 italic">"{{ $order->customer_note }}"</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right: Status Control Center & Audit History (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Update Order Status Control Form -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">
                    Kelola Status Pemenuhan Pesanan
                </h3>

                @if(!$order->isPaid() && $order->order_status !== 'cancelled')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                        <span class="font-bold">⚠️ Perhatian Otorisasi:</span>
                        <p>Pesanan belum berstatus Lunas (PAID). Sesuai aturan bisnis, status tidak dapat dimajukan ke Produksi/Pengiriman sebelum pembayaran diverifikasi sah.</p>
                        @if($order->isPendingReview())
                            <a href="{{ route('admin.payments.show', $order->id) }}" class="inline-block mt-1 font-bold text-amber-700 underline">
                                Periksa Bukti Transfer Sekarang &rarr;
                            </a>
                        @endif
                    </div>
                @endif

                <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Baru Pesanan</label>
                        <select name="order_status" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                            <option value="pending_payment" {{ $order->order_status === 'pending_payment' ? 'selected' : '' }}>Pending Payment (Menunggu Bayar)</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }} {{ !$order->isPaid() ? 'disabled' : '' }}>Processing (Dikonfirmasi Lunas)</option>
                            <option value="in_production" {{ $order->order_status === 'in_production' ? 'selected' : '' }} {{ !$order->isPaid() ? 'disabled' : '' }}>Sedang Dirangkai (Produksi)</option>
                            <option value="ready_to_ship" {{ $order->order_status === 'ready_to_ship' ? 'selected' : '' }} {{ !$order->isPaid() ? 'disabled' : '' }}>Siap Dikirim / Diambil</option>
                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }} {{ !$order->isPaid() ? 'disabled' : '' }}>Dalam Pengiriman (Kurir)</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }} {{ !$order->isPaid() ? 'disabled' : '' }}>Selesai (Completed)</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Resi / Info Kurir (Wajib saat Shipped)</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Contoh: JNE-9082312 / Kurir Toko (Pak Budi)"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan Perubahan Status (Akan Dicatat di Audit Log)</label>
                        <input type="text" name="notes" placeholder="Contoh: Bunga selesai dirangkai & dipacking aman"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition-colors">
                        Simpan Perubahan Status
                    </button>
                </form>
            </div>

            <!-- Audit History Timeline -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">
                    Riwayat Aktivitas & Perubahan
                </h3>

                <div class="space-y-4">
                    @foreach($order->statusHistories as $history)
                        <div class="relative pl-6 pb-4 border-l-2 border-slate-200 last:border-0 last:pb-0 text-xs">
                            <div class="absolute -left-1.5 top-0 w-3 h-3 rounded-full bg-slate-900"></div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</span>
                                <span class="text-[10px] text-slate-400">{{ $history->created_at->format('d M, H:i') }}</span>
                            </div>
                            @if($history->actor)
                                <span class="text-[10px] text-slate-500 block">Oleh: {{ $history->actor->name }}</span>
                            @endif
                            @if($history->notes)
                                <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $history->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
