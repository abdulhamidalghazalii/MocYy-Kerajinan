@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Ringkasan Statistik & Penjualan')

@section('content')
<div class="space-y-8">
    
    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Omzet Sah (Paid Only!) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Omzet Sah</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                @currency($totalRevenue)
            </div>
            <p class="text-[11px] text-emerald-600 font-medium mt-1">
                ✓ Hanya dari pembayaran yang telah terkonfirmasi sah
            </p>
        </div>

        <!-- Omzet Hari Ini -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Omzet Hari Ini</span>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                @currency($revenueToday)
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
                {{ $ordersTodayCount }} pesanan masuk hari ini
            </p>
        </div>

        <!-- Menunggu Verifikasi Pembayaran (CRITICAL ALERT) -->
        <div class="bg-white p-6 rounded-3xl border-2 {{ $pendingReviewCount > 0 ? 'border-amber-400 bg-amber-50/20' : 'border-slate-200' }} shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Perlu Verifikasi</span>
                <span class="p-2 bg-amber-100 text-amber-700 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-900">
                {{ $pendingReviewCount }} Bukti Transfer
            </div>
            <a href="{{ route('admin.payments.verification') }}" class="text-[11px] font-bold text-amber-700 hover:underline block mt-1">
                Periksa & Konfirmasi Sekarang &rarr;
            </a>
        </div>

        <!-- Sedang Dikerjakan (Produksi / Siap Kirim) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dalam Pengerjaan</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                {{ $processingCount + $inProductionCount + $readyToShipCount }} Buket
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
                {{ $readyToShipCount }} buket siap dikirim kurir
            </p>
        </div>

    </div>

    <!-- Secondary Status Summary Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-slate-400 block mb-1">Menunggu Pembayaran:</span>
            <span class="text-lg font-bold text-amber-600">{{ $pendingPaymentCount }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-slate-400 block mb-1">Pembayaran Ditolak:</span>
            <span class="text-lg font-bold text-rose-600">{{ $rejectedPaymentCount }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-slate-400 block mb-1">Sedang Dalam Pengiriman:</span>
            <span class="text-lg font-bold text-cyan-600">{{ $shippedCount }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-slate-400 block mb-1">Stok Menipis (&le; 5):</span>
            <span class="text-lg font-bold text-orange-600">{{ $lowStockProducts->count() }}</span>
        </div>
    </div>

    <!-- Visual Sales Chart & Low Stock Products -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Sales Chart (7 days) (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-base text-slate-900">Grafik Omzet Penjualan Terkonfirmasi (7 Hari Terakhir)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Hanya menghitung pembayaran yang telah berstatus PAID</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-rose-500 hover:underline">
                    Lihat Laporan Lengkap &rarr;
                </a>
            </div>

            <!-- CSS Bar Chart -->
            @php
                $maxRevenue = max(array_merge($chartRevenues, [100000]));
            @endphp
            <div class="h-64 flex items-end justify-between gap-3 pt-6 px-2">
                @foreach($chartDates as $index => $label)
                    @php
                        $rev = $chartRevenues[$index];
                        $heightPercent = $maxRevenue > 0 ? min(100, round(($rev / $maxRevenue) * 100)) : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            Rp {{ number_format($rev / 1000, 0) }}k
                        </span>
                        <div class="w-full bg-slate-100 rounded-t-xl overflow-hidden h-44 flex items-end">
                            <div style="height: {{ max(4, $heightPercent) }}%;"
                                class="w-full bg-gradient-to-t from-[#D48C9A] to-[#E07A5F] rounded-t-xl group-hover:from-rose-500 group-hover:to-orange-500 transition-all duration-300">
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 mt-1 truncate">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Low Stock Alert Box (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-3 border-b border-slate-100 flex items-center justify-between">
                <span>Perhatian Stok Produk</span>
                <span class="text-xs text-rose-500 font-semibold">&le; 5 Unit</span>
            </h2>

            @if($lowStockProducts->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Seluruh stok produk berada dalam batas aman.</p>
            @else
                <div class="space-y-3">
                    @foreach($lowStockProducts as $prod)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="w-10 h-10 rounded-xl object-cover">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 truncate max-w-[130px]">{{ $prod->name }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $prod->sku }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-rose-100 text-rose-800 font-bold text-xs rounded-lg shrink-0">
                                Sisa {{ $prod->stock }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="font-bold text-base text-slate-900">Transaksi & Pesanan Terbaru</h2>
                <p class="text-xs text-slate-400">Ringkasan 8 pesanan terakhir yang masuk ke sistem</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-rose-500 hover:underline">
                Kelola Seluruh Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-y border-slate-100">
                    <tr>
                        <th class="py-3 px-4">No. Pesanan</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4">Status Pembayaran</th>
                        <th class="py-3 px-4">Status Pesanan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 block">{{ $order->user?->name ?? $order->recipient_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $order->created_at->format('d M, H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                @currency($order->grand_total)
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $order->paymentMethod?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $order->payment_status_badge_class }}">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $order->order_status_badge_class }}">
                                    {{ $order->order_status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                @if($order->isPendingReview())
                                    <a href="{{ route('admin.payments.show', $order->id) }}" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-[10px]">
                                        Verifikasi
                                    </a>
                                @endif
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[10px]">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
