@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya | MocYy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-gray-900">Riwayat Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau status pembayaran, proses merangkai bunga, dan pengiriman paket hadiah Anda.</p>
        </div>

        <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-[#D48C9A] hover:bg-[#c67f7e] text-white rounded-xl text-xs font-bold transition-all shadow-xs self-start sm:self-auto">
            + Pesan Buket Lainnya
        </a>
    </div>

    <!-- Status Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 text-xs font-semibold">
        <a href="{{ route('customer.orders.index') }}" class="px-4 py-2 rounded-xl border transition-all shrink-0 {{ !request('status') && !request('payment_status') ? 'bg-[#D48C9A] text-white border-[#D48C9A]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Semua Pesanan
        </a>
        <a href="{{ route('customer.orders.index', ['payment_status' => 'unpaid']) }}" class="px-4 py-2 rounded-xl border transition-all shrink-0 {{ request('payment_status') === 'unpaid' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Menunggu Pembayaran
        </a>
        <a href="{{ route('customer.orders.index', ['payment_status' => 'pending_review']) }}" class="px-4 py-2 rounded-xl border transition-all shrink-0 {{ request('payment_status') === 'pending_review' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Verifikasi Admin
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'processing']) }}" class="px-4 py-2 rounded-xl border transition-all shrink-0 {{ request('status') === 'processing' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Sedang Dirangkai / Diproses
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl border transition-all shrink-0 {{ request('status') === 'completed' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Selesai
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl border border-[#F4EDE0] p-12 text-center max-w-md mx-auto space-y-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 text-[#D48C9A] flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-gray-900">Tidak ada pesanan ditemukan</h3>
            <p class="text-xs text-gray-500">Tidak ada pesanan dengan kriteria status ini.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-3xl border border-[#F4EDE0] p-6 shadow-sm hover:border-rose-200 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-gray-900">{{ $order->order_number }}</span>
                                <span class="text-xs text-gray-400">• {{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                Metode: <strong>{{ $order->paymentMethod?->name }}</strong> • Kurir: {{ $order->shippingMethod?->name }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $order->payment_status_badge_class }}">
                                {{ $order->payment_status_label }}
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $order->order_status_badge_class }}">
                                {{ $order->order_status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="py-4 space-y-2">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-gray-800">{{ $item->product_name }}</span>
                                    @if($item->variant_info)
                                        <span class="text-[11px] text-gray-400">({{ $item->variant_info }})</span>
                                    @endif
                                    <span class="text-gray-400">x{{ $item->quantity }}</span>
                                </div>
                                <span class="font-mono text-gray-600">@currency($item->subtotal)</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Action -->
                    <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-gray-400 block">Total Pembayaran</span>
                            <span class="text-base font-bold text-[#E07A5F]">@currency($order->grand_total)</span>
                        </div>

                        <div class="flex items-center gap-2.5">
                            @if($order->canUploadPaymentProof())
                                <a href="{{ route('customer.orders.payment', $order->id) }}" class="px-4 py-2 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] text-white text-xs font-bold rounded-xl shadow-xs hover:scale-102 transition-transform">
                                    Bayar / Upload Bukti
                                </a>
                            @endif

                            <a href="{{ route('customer.orders.show', $order->id) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                Rincian Pesanan &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
