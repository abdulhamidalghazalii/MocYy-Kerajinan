@extends('layouts.app')

@section('title', 'Dashboard Customer | ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <!-- User Welcome Banner -->
    <div class="bg-gradient-to-r from-rose-50 via-white to-rose-50/50 p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#D48C9A] to-[#E07A5F] text-white font-serif font-bold text-2xl flex items-center justify-center shadow-md shadow-rose-200">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Halo, {{ $user->name }}!</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $user->email }} • {{ $user->phone ?: 'Belum ada nomor telepon' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customer.profile.edit') }}" class="px-4 py-2 bg-white border border-rose-200 hover:bg-rose-50 text-gray-700 rounded-xl text-xs font-bold transition-all shadow-2xs">
                Edit Profil & Alamat
            </a>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-[#D48C9A] hover:bg-[#c67f7e] text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                Pesan Buket Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
        <div class="bg-white p-5 rounded-2xl border border-[#F4EDE0] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Total Pesanan</span>
            <span class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</span>
            <span class="text-[11px] text-gray-400 block mt-1">Seluruh riwayat</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/30">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block mb-1">Belum Dibayar</span>
            <span class="text-2xl font-bold text-amber-800">{{ $stats['pending_payment'] }}</span>
            <span class="text-[11px] text-amber-600 block mt-1">Menunggu transfer</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-xs bg-blue-50/30">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block mb-1">Verifikasi Admin</span>
            <span class="text-2xl font-bold text-blue-800">{{ $stats['pending_review'] }}</span>
            <span class="text-[11px] text-blue-600 block mt-1">Bukti sedang diperiksa</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-indigo-200 shadow-xs bg-indigo-50/30">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 block mb-1">Sedang Dikerjakan</span>
            <span class="text-2xl font-bold text-indigo-800">{{ $stats['processing'] }}</span>
            <span class="text-[11px] text-indigo-600 block mt-1">Proses merangkai & kirim</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Recent Orders (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="font-serif text-lg font-bold text-gray-900">Pesanan Terbaru Anda</h2>
                <a href="{{ route('customer.orders.index') }}" class="text-xs font-bold text-[#D48C9A] hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="text-center py-10 space-y-3">
                    <p class="text-sm text-gray-400">Anda belum memiliki riwayat pesanan.</p>
                    <a href="{{ route('products.index') }}" class="inline-block px-5 py-2.5 bg-[#D48C9A] text-white text-xs font-bold rounded-xl shadow-xs">
                        Pesan Hadiah Sekarang
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-100 space-y-4">
                    @foreach($recentOrders as $order)
                        <div class="pt-4 first:pt-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-gray-900">{{ $order->order_number }}</span>
                                    <span class="text-xs text-gray-400">• {{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-600">
                                    Penerima: <strong>{{ $order->recipient_name }}</strong> ({{ $order->city }})
                                </p>
                                <div class="flex items-center gap-2 pt-1">
                                    <!-- Payment badge -->
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $order->payment_status_badge_class }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                    <!-- Order status badge -->
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $order->order_status_badge_class }}">
                                        {{ $order->order_status_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-left sm:text-right space-y-2 self-stretch sm:self-center">
                                <span class="font-bold text-sm text-[#E07A5F] block">@currency($order->grand_total)</span>
                                
                                <div class="flex items-center gap-2 justify-start sm:justify-end">
                                    @if($order->canUploadPaymentProof())
                                        <a href="{{ route('customer.orders.payment', $order->id) }}" class="px-3 py-1.5 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] text-white text-xs font-bold rounded-lg shadow-xs hover:scale-102 transition-transform">
                                            Bayar / Upload Bukti
                                        </a>
                                    @endif
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- In-App Notifications (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
            <h2 class="font-serif text-lg font-bold text-gray-900 pb-4 border-b border-gray-100 flex items-center justify-between">
                <span>Notifikasi Akun</span>
                <span class="text-xs text-[#D48C9A] font-semibold">Terkini</span>
            </h2>

            @if($notifications->isEmpty())
                <p class="text-xs text-gray-400 text-center py-6">Belum ada notifikasi baru.</p>
            @else
                <div class="space-y-3">
                    @foreach($notifications as $notif)
                        <div class="p-3.5 rounded-2xl text-xs space-y-1 {{ $notif->type === 'success' ? 'bg-emerald-50/70 border border-emerald-100 text-emerald-900' : ($notif->type === 'error' ? 'bg-rose-50/70 border border-rose-100 text-rose-900' : 'bg-gray-50 border border-gray-100 text-gray-800') }}">
                            <div class="flex items-center justify-between">
                                <span class="font-bold">{{ $notif->title }}</span>
                                <span class="text-[10px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-gray-600 leading-relaxed">{{ $notif->message }}</p>
                            @if($notif->action_url)
                                <a href="{{ $notif->action_url }}" class="inline-block mt-1 font-bold text-[#D48C9A] hover:underline text-[11px]">
                                    Lihat Pesanan &rarr;
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
