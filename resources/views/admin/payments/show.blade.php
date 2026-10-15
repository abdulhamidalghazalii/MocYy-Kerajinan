@extends('layouts.admin')

@section('title', 'Detail Verifikasi ' . $order->order_number)
@section('page_title', 'Periksa Bukti Pembayaran: ' . $order->order_number)

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <a href="{{ route('admin.payments.verification') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Daftar Verifikasi
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $order->payment_status_badge_class }}">
                {{ $order->payment_status_label }}
            </span>
        </div>
    </div>

    @php
        $payment = $order->latestPayment;
        $latestProof = $payment?->latestProof;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Uploaded Proof Viewer (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="font-bold text-base text-slate-900">File Bukti Transfer (Private Storage)</h2>
                @if($latestProof)
                    <a href="{{ route('admin.payments.view_proof', $latestProof->id) }}" target="_blank" class="text-xs font-bold text-rose-600 hover:underline">
                        Buka di Tab Baru &rarr;
                    </a>
                @endif
            </div>

            @if($latestProof)
                <div class="space-y-4">
                    <!-- Image or PDF display -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center min-h-[400px]">
                        @if($latestProof->isImage())
                            <img src="{{ route('admin.payments.view_proof', $latestProof->id) }}" alt="Bukti Transfer" class="max-h-[600px] w-auto object-contain mx-auto">
                        @else
                            <div class="p-8 text-center space-y-3">
                                <svg class="w-16 h-16 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <p class="text-xs font-bold text-slate-800">Dokumen Bukti PDF</p>
                                <a href="{{ route('admin.payments.view_proof', $latestProof->id) }}" target="_blank" class="inline-block px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold">
                                    Unduh & Buka Dokumen PDF
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Metadata -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1 text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Nama File Asli:</span>
                            <span class="font-bold text-slate-900">{{ $latestProof->original_filename }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Ukuran & Format:</span>
                            <span>{{ $latestProof->formatted_file_size }} ({{ $latestProof->mime_type }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Waktu Upload:</span>
                            <span class="font-bold text-slate-900">{{ $latestProof->uploaded_at->format('d M Y, H:i:s') }} WIB</span>
                        </div>
                        @if($latestProof->sender_account_name)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Pengirim:</span>
                                <span class="font-bold text-slate-900">{{ $latestProof->sender_account_name }}</span>
                            </div>
                        @endif
                        @if($latestProof->notes)
                            <div class="pt-2 border-t border-slate-200">
                                <span class="text-slate-400 block">Catatan Customer:</span>
                                <p class="italic text-slate-800">"{{ $latestProof->notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-400 text-xs">
                    Customer belum mengunggah bukti pembayaran.
                </div>
            @endif
        </div>

        <!-- Right: Verification Decisions & Order Summary (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Decision Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">
                    Keputusan Verifikasi Admin
                </h3>

                @if($order->isPendingReview())
                    <!-- Confirm Form -->
                    <form action="{{ route('admin.payments.confirm', $order->id) }}" method="POST" class="space-y-3" onsubmit="return confirm('Apakah Anda telah memeriksa mutasi dan memastikan uang telah masuk?')">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                No. Referensi Transaksi Bank (Opsional)
                            </label>
                            <input type="text" name="transaction_ref" placeholder="Contoh: DANA-0982312"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                            ✓ Konfirmasi Pembayaran Sah (PAID)
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('admin.payments.reject', $order->id) }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100" onsubmit="return confirm('Tolak pembayaran ini?')">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-rose-800 mb-1">
                                Alasan Penolakan Pembayaran *
                            </label>
                            <textarea name="rejection_reason" rows="2" required placeholder="Contoh: Nominal transfer tidak cocok dengan tagihan..."
                                class="w-full p-2.5 bg-slate-50 border border-rose-200 rounded-xl text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl text-xs transition-colors">
                            ✗ Tolak Pembayaran
                        </button>
                    </form>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Pembayaran:</span>
                            <span class="font-bold {{ $order->isPaid() ? 'text-emerald-700' : 'text-rose-700' }}">{{ $order->payment_status_label }}</span>
                        </div>
                        @if($payment?->verifier)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Diverifikasi oleh:</span>
                                <span class="font-bold text-slate-900">{{ $payment->verifier->name }}</span>
                            </div>
                        @endif
                        @if($payment?->verified_at)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Waktu:</span>
                                <span>{{ $payment->verified_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if($payment?->transaction_ref)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ref Transaksi:</span>
                                <span class="font-mono font-bold">{{ $payment->transaction_ref }}</span>
                            </div>
                        @endif
                        @if($payment?->rejection_reason)
                            <div class="pt-2 border-t border-slate-200">
                                <span class="text-slate-400 block">Alasan Penolakan:</span>
                                <p class="text-rose-700 font-semibold">{{ $payment->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Order Financial Overview -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-bold text-sm text-slate-900 pb-2 border-b border-slate-100">
                    Rincian Tagihan Pesanan
                </h3>

                <div class="text-xs space-y-2">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Produk:</span>
                        <span class="font-bold text-slate-900">@currency($order->subtotal)</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim ({{ $order->shippingMethod?->name }}):</span>
                        <span class="font-bold text-slate-900">@currency($order->shipping_cost)</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex justify-between text-sm">
                        <span class="font-bold text-slate-900">Total Wajib Masuk:</span>
                        <span class="font-bold text-[#E07A5F] text-base">@currency($order->grand_total)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
