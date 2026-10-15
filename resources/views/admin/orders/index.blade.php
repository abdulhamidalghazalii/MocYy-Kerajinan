@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')
@section('page_title', 'Daftar Seluruh Pesanan')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Cari Pesanan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Pesanan / Nama..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                    <option value="">Semua Status Bayar</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="pending_review" {{ request('payment_status') === 'pending_review' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Pemenuhan</label>
                <select name="status" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                    <option value="">Semua Status Pesanan</option>
                    <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="in_production" {{ request('status') === 'in_production' ? 'selected' : '' }}>Sedang Dirangkai</option>
                    <option value="ready_to_ship" {{ request('status') === 'ready_to_ship' ? 'selected' : '' }}>Siap Kirim</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.orders.index') }}" class="py-2.5 px-3 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center text-slate-400 text-sm">
                Tidak ada pesanan yang sesuai filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">No. Pesanan</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Customer</th>
                            <th class="py-3.5 px-4">Penerima & Kota</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4">Pembayaran</th>
                            <th class="py-3.5 px-4">Status Pesanan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($orders as $ord)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $ord->order_number }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    {{ $ord->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-slate-900 block">{{ $ord->user?->name ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $ord->user?->phone }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $ord->recipient_name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $ord->city }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    @currency($ord->grand_total)
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ord->payment_status_badge_class }}">
                                        {{ $ord->payment_status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ord->order_status_badge_class }}">
                                        {{ $ord->order_status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                                        Kelola &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
