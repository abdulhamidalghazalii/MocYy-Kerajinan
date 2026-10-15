@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Penjualan')
@section('page_title', 'Laporan Penjualan & Rekap Finansial')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold">
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Hanya Lunas (PAID - Omzet Valid)</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="waiting_verification" {{ $status === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition-colors text-center">
                    Filter Laporan
                </button>
                <a href="{{ route('admin.reports.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'payment_status' => $status]) }}" class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5" title="Ekspor ke CSV">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>CSV</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-700 text-white p-6 rounded-3xl shadow-md">
            <span class="text-xs uppercase font-bold tracking-wider opacity-90 block">Total Omzet Bersih (Lunas)</span>
            <span class="font-serif text-3xl font-bold block mt-2">Rp {{ number_format($totalPaidRevenue, 0, ',', '.') }}</span>
            <span class="text-[11px] opacity-80 mt-1 block">Hanya menghitung transaksi lunas diverifikasi</span>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs uppercase font-bold tracking-wider text-slate-500 block">Pesanan Lunas (Volume)</span>
            <span class="font-serif text-3xl font-bold text-slate-900 block mt-2">{{ number_format($totalPaidOrdersCount) }} Buket</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Volume transaksi berhasil</span>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <span class="text-xs uppercase font-bold tracking-wider text-slate-500 block">Rata-rata Nilai Pesanan (AOV)</span>
            <span class="font-serif text-3xl font-bold text-slate-900 block mt-2">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Rata-rata belanja per customer</span>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Rincian Transaksi Terfilter</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $orders->total() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                        <th class="p-3.5 pl-5">Nomor Order & Tanggal</th>
                        <th class="p-3.5">Customer</th>
                        <th class="p-3.5">Metode Bayar</th>
                        <th class="p-3.5">Status Bayar</th>
                        <th class="p-3.5">Status Pesanan</th>
                        <th class="p-3.5 text-right pr-5">Grand Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-3.5 pl-5">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-rose-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <span class="block text-[11px] text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-800 block">{{ $order->recipient_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $order->recipient_phone }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-700">{{ $order->paymentMethod?->name ?? 'DANA / QRIS' }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($order->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">LUNAS</span>
                                @elseif($order->payment_status === 'waiting_verification')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">VERIFIKASI</span>
                                @elseif($order->payment_status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 font-bold text-[10px]">DITOLAK</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px]">{{ strtoupper($order->payment_status) }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right pr-5 font-bold font-mono text-slate-900">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tidak ada transaksi yang sesuai dengan rentang tanggal dan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
