@extends('layouts.admin')

@section('title', 'Detail Akun Customer')
@section('page_title', 'Detail Akun & Riwayat Pesanan Pelanggan')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Customer</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Akun / Email</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-400 text-white font-bold flex items-center justify-center text-xl shadow-md">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ $customer->name }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $customer->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $customer->is_active ? 'Akun Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 text-[11px] font-bold uppercase">Alamat Email</span>
                    <p class="font-mono font-semibold text-slate-800 mt-0.5">{{ $customer->email }}</p>
                </div>

                <div>
                    <span class="text-slate-400 text-[11px] font-bold uppercase">Nomor WhatsApp / HP</span>
                    <p class="font-semibold text-slate-800 mt-0.5">
                        {{ $customer->phone ?: 'Belum diisi' }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400 text-[11px] font-bold uppercase">Tanggal Registrasi</span>
                    <p class="text-slate-700 mt-0.5">{{ $customer->created_at->format('d F Y, H:i') }} WIB</p>
                </div>

                <div>
                    <span class="text-slate-400 text-[11px] font-bold uppercase">Total Pesanan</span>
                    <p class="font-bold text-slate-900 mt-0.5">{{ $customer->orders->count() }} Transaksi</p>
                </div>
            </div>
        </div>

        <!-- Orders & Addresses History (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order History List -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h4 class="text-sm font-bold text-slate-900 font-serif">Riwayat Pesanan Pelanggan</h4>

                @if($customer->orders->isEmpty())
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada transaksi belanja dari customer ini.</p>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($customer->orders as $order)
                            <div class="py-3.5 flex items-center justify-between text-xs hover:bg-slate-50/50 rounded-xl px-2 transition">
                                <div>
                                    <div class="font-bold text-slate-900">{{ $order->order_number }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                </div>

                                <div class="text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        Bayar: {{ $order->payment_status }}
                                    </span>
                                </div>

                                <div class="text-right">
                                    <div class="font-extrabold text-slate-900">@currency($order->grand_total)</div>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-[11px] text-rose-600 hover:underline font-semibold">
                                        Detail Pesanan &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Addresses List -->
            @if($customer->addresses->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                    <h4 class="text-sm font-bold text-slate-900 font-serif">Alamat Pengiriman Tersimpan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach($customer->addresses as $addr)
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1">
                                <div class="font-bold text-slate-800 flex items-center justify-between">
                                    <span>{{ $addr->label }}</span>
                                    @if($addr->is_default)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold">Utama</span>
                                    @endif
                                </div>
                                <div class="text-slate-600">{{ $addr->recipient_name }} ({{ $addr->recipient_phone }})</div>
                                <div class="text-[11px] text-slate-500">{{ $addr->full_address }}, {{ $addr->city }} {{ $addr->postal_code }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
