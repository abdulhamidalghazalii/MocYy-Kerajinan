@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran Manual')
@section('page_title', 'Pusat Verifikasi Pembayaran (QRIS / DANA)')

@section('content')
<div class="space-y-6"
    x-data="{
        confirmModalOpen: false,
        rejectModalOpen: false,
        activeOrder: null,
        confirmActionUrl: '',
        rejectActionUrl: '',
        openConfirm(order) {
            this.activeOrder = order;
            this.confirmActionUrl = '/admin/verifikasi-pembayaran/' + order.id + '/konfirmasi';
            this.confirmModalOpen = true;
        },
        openReject(order) {
            this.activeOrder = order;
            this.rejectActionUrl = '/admin/verifikasi-pembayaran/' + order.id + '/tolak';
            this.rejectModalOpen = true;
        }
    }">

    <!-- Header & Info Notice -->
    <div class="bg-amber-50 border border-amber-200 p-5 rounded-3xl text-amber-900 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold text-amber-900 text-sm">Prinsip Keamanan Verifikasi:</p>
                <p class="mt-0.5 text-amber-800 leading-relaxed font-light">
                    Unggahan bukti transfer dari customer bukan bukti mutlak bahwa dana telah diterima. <strong>Admin wajib memeriksa mutasi rekening atau aplikasi merchant DANA/QRIS</strong> sebelum menekan tombol konfirmasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search -->
    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <!-- Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 text-xs font-semibold">
                <a href="{{ route('admin.payments.verification', ['status' => 'pending_review']) }}"
                    class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-2 {{ $status === 'pending_review' ? 'bg-amber-500 text-slate-950 border-amber-500 font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    <span>Menunggu Verifikasi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'pending_review' ? 'bg-slate-950 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['pending_review'] }}
                    </span>
                </a>

                <a href="{{ route('admin.payments.verification', ['status' => 'paid']) }}"
                    class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-2 {{ $status === 'paid' ? 'bg-emerald-600 text-white border-emerald-600 font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    <span>Terkonfirmasi (Lunas)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'paid' ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['paid'] }}
                    </span>
                </a>

                <a href="{{ route('admin.payments.verification', ['status' => 'rejected']) }}"
                    class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-2 {{ $status === 'rejected' ? 'bg-rose-600 text-white border-rose-600 font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    <span>Ditolak</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'rejected' ? 'bg-rose-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['rejected'] }}
                    </span>
                </a>

                <a href="{{ route('admin.payments.verification', ['status' => 'all']) }}"
                    class="px-3.5 py-2 rounded-xl border transition-all shrink-0 {{ $status === 'all' ? 'bg-slate-900 text-white border-slate-900 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    Semua
                </a>
            </div>

            <!-- Search input -->
            <form method="GET" action="{{ route('admin.payments.verification') }}" class="relative min-w-[240px]">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Pesanan / Customer..."
                    class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-rose-400 focus:outline-none">
                <button type="submit" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>

        </div>
    </div>

    <!-- Verification Table (Section 5.C) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center text-slate-400 space-y-3">
                <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-semibold">Tidak ada bukti pembayaran dengan status ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">No. Pesanan & Tanggal</th>
                            <th class="py-3.5 px-4">Customer</th>
                            <th class="py-3.5 px-4">Total Tagihan</th>
                            <th class="py-3.5 px-4">Metode Bayar</th>
                            <th class="py-3.5 px-4">Waktu Upload</th>
                            <th class="py-3.5 px-4">Bukti Transfer (Privat)</th>
                            <th class="py-3.5 px-4">Status Verifikasi</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($orders as $ord)
                            @php
                                $payment = $ord->latestPayment;
                                $latestProof = $payment?->latestProof;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Order Number & Date -->
                                <td class="py-4 px-4">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="font-mono font-bold text-slate-900 hover:text-rose-600 underline decoration-slate-300">
                                        {{ $ord->order_number }}
                                    </a>
                                    <span class="block text-[11px] text-slate-400 mt-0.5">
                                        {{ $ord->created_at->format('d M Y, H:i') }}
                                    </span>
                                </td>

                                <!-- Customer -->
                                <td class="py-4 px-4">
                                    <span class="font-semibold text-slate-900 block">{{ $ord->user?->name ?? $ord->recipient_name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $ord->user?->email }}</span>
                                </td>

                                <!-- Total Tagihan -->
                                <td class="py-4 px-4 font-bold text-slate-900 text-sm">
                                    @currency($ord->grand_total)
                                </td>

                                <!-- Metode Bayar -->
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold {{ $ord->paymentMethod?->code === 'dana' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $ord->paymentMethod?->name ?? 'Manual' }}
                                    </span>
                                </td>

                                <!-- Waktu Upload -->
                                <td class="py-4 px-4">
                                    @if($latestProof)
                                        <span class="text-slate-800 font-semibold block">{{ $latestProof->uploaded_at->format('d M Y, H:i') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $latestProof->uploaded_at->diffForHumans() }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum diunggah</span>
                                    @endif
                                </td>

                                <!-- Thumbnail & Tombol Lihat Bukti Aman -->
                                <td class="py-4 px-4">
                                    @if($latestProof)
                                        <div class="flex items-center gap-2">
                                            @if($latestProof->isImage())
                                                <a href="{{ route('admin.payments.view_proof', $latestProof->id) }}" target="_blank" class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-300 block shrink-0 hover:scale-105 transition-transform" title="Klik untuk memperbesar">
                                                    <img src="{{ route('admin.payments.view_proof', $latestProof->id) }}" alt="Bukti" class="w-full h-full object-cover">
                                                </a>
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-200 flex items-center justify-center font-bold text-[10px] shrink-0">
                                                    PDF
                                                </div>
                                            @endif

                                            <div>
                                                <a href="{{ route('admin.payments.view_proof', $latestProof->id) }}" target="_blank" class="text-xs font-bold text-rose-600 hover:underline block truncate max-w-[100px]">
                                                    Buka File
                                                </a>
                                                <span class="text-[10px] text-slate-400 block">{{ $latestProof->formatted_file_size }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>

                                <!-- Status Verifikasi -->
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $ord->payment_status_badge_class }}">
                                        {{ $ord->payment_status_label }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($ord->isPendingReview())
                                            <!-- Konfirmasi Pembayaran Button -->
                                            <button type="button" @click="openConfirm({ id: {{ $ord->id }}, number: '{{ $ord->order_number }}', total: '{{ (int)$ord->grand_total }}' })"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors shadow-2xs">
                                                Konfirmasi
                                            </button>

                                            <!-- Tolak Pembayaran Button -->
                                            <button type="button" @click="openReject({ id: {{ $ord->id }}, number: '{{ $ord->order_number }}' })"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition-colors">
                                                Tolak
                                            </button>
                                        @else
                                            <a href="{{ route('admin.payments.show', $ord->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-colors">
                                                Detail
                                            </a>
                                        @endif
                                    </div>
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

    <!-- Confirm Payment Modal Dialog -->
    <div x-show="confirmModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div @click.outside="confirmModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h3 class="font-serif text-lg font-bold text-slate-900">Konfirmasi Pembayaran Sah</h3>
                    <p class="text-xs text-slate-500" x-text="'Pesanan: ' + (activeOrder ? activeOrder.number : '')"></p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed font-light">
                Pastikan Anda telah memeriksa mutasi rekening bank atau akun merchant DANA dan dana telah <strong>benar-benar masuk</strong> ke rekening toko.
            </p>

            <form :action="confirmActionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor Referensi / Catatan Transaksi Bank (Opsional)
                    </label>
                    <input type="text" name="transaction_ref" placeholder="Contoh: REF9832104 / Mutasi DANA Masuk"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="confirmModalOpen = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs">
                        Ya, Sah & Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Payment Modal Dialog -->
    <div x-show="rejectModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div @click.outside="rejectModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div>
                    <h3 class="font-serif text-lg font-bold text-slate-900">Tolak Bukti Pembayaran</h3>
                    <p class="text-xs text-slate-500" x-text="'Pesanan: ' + (activeOrder ? activeOrder.number : '')"></p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed font-light">
                Customer akan menerima notifikasi penolakan beserta alasan yang Anda berikan, dan dapat mengunggah ulang bukti pembayaran yang sah.
            </p>

            <form :action="rejectActionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-rose-800 mb-1">
                        Alasan Penolakan Pembayaran (Wajib Diisi) *
                    </label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Contoh: Nominal transfer kurang Rp 50.000 / Bukti transfer buram / Nama penerima salah"
                        class="w-full p-3 bg-slate-50 border border-rose-200 rounded-xl text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs">
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
