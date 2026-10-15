@extends('layouts.app')

@section('title', 'Pembayaran Pesanan #' . $order->order_number . ' | MocYy')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
    x-data="{
        copiedAmount: false,
        copiedAccount: false,
        filePreview: null,
        fileName: '',
        fileSize: '',
        copyText(text, type) {
            navigator.clipboard.writeText(text);
            if(type === 'amount') { this.copiedAmount = true; setTimeout(() => this.copiedAmount = false, 2500); }
            if(type === 'account') { this.copiedAccount = true; setTimeout(() => this.copiedAccount = false, 2500); }
        },
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            this.fileSize = (file.size / 1024 > 1024) 
                ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                : (file.size / 1024).toFixed(0) + ' KB';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.filePreview = event.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.filePreview = null;
            }
        }
    }">

    <!-- Header & Status Reminder -->
    <div class="text-center space-y-2 mb-8">
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold shadow-xs">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            Menunggu Pembayaran & Upload Bukti Transfer
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-gray-900">Pembayaran QRIS Nasional</h1>
        <p class="text-sm text-gray-500">Nomor Pesanan: <strong class="font-mono text-gray-900">{{ $order->order_number }}</strong></p>
    </div>

    <!-- Payment Main Card -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        
        <!-- Left: QR Code & Account Details (6 cols) -->
        <div class="md:col-span-6 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6 text-center">
            
            <!-- Amount to Pay with Copy Button -->
            <div class="p-4 bg-gradient-to-tr from-rose-50 to-amber-50/40 rounded-2xl border border-rose-100 space-y-1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Tagihan Tepat</span>
                <div class="flex items-center justify-center gap-2">
                    <span class="font-serif text-3xl font-bold text-[#E07A5F]">@currency($order->grand_total)</span>
                    <button type="button" @click="copyText('{{ (int)$order->grand_total }}', 'amount')" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-white transition-colors" title="Salin Nominal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
                <span x-show="copiedAmount" class="text-[11px] font-bold text-emerald-600 block" style="display: none;">Nominal disalin ke clipboard!</span>
                <p class="text-[11px] text-gray-400">Pastikan transfer dengan nominal tepat agar cepat diverifikasi.</p>
            </div>

            <!-- QR Code Display (Real MocYy Kerajinan QRIS) -->
            @php
                $method = $order->paymentMethod;
                $qrUrl = $method?->qr_image_url ?? asset('images/qris_mocyy.jpg');
            @endphp
            <div class="space-y-4">
                <div class="relative group inline-block p-4 bg-white rounded-3xl border-2 border-dashed border-rose-200 shadow-md hover:border-rose-300 transition-colors">
                    <img src="{{ $qrUrl }}" alt="QRIS MocYy Kerajinan" class="w-64 max-w-full h-auto object-contain mx-auto rounded-xl cursor-zoom-in"
                         @click="$store.lightbox.open('{{ $qrUrl }}', 'QRIS MocYy, Kerajinan (NMID: ID1026613806474)')">
                    
                    <button type="button" 
                            @click="$store.lightbox.open('{{ $qrUrl }}', 'QRIS MocYy, Kerajinan (NMID: ID1026613806474)')"
                            class="absolute top-6 right-6 p-2 rounded-full bg-black/60 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-xs"
                            title="Perbesar QRIS">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                    </button>
                </div>

                <!-- Download QR Button for Mobile Users -->
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ $qrUrl }}" download="QRIS_MocYy_Kerajinan.jpg" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-[#8C534A] rounded-xl font-bold text-xs transition-colors shadow-xs">
                        <svg class="w-4 h-4 text-[#D48C9A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh / Simpan QR</span>
                    </a>
                    <button type="button" @click="$store.lightbox.open('{{ $qrUrl }}', 'QRIS MocYy, Kerajinan (NMID: ID1026613806474)')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl font-bold text-xs transition-colors border border-gray-200 shadow-xs">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Perbesar Layar</span>
                    </button>
                </div>
                
                <div>
                    <span class="text-sm font-bold text-gray-900 block">MocYy, Kerajinan</span>
                    <span class="text-xs text-gray-500 block">NMID : ID1026613806474 (A01)</span>
                    <span class="inline-block mt-1 px-3 py-0.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-full">
                        Satu QRIS untuk Semua (DANA, BCA, GoPay, OVO, ShopeePay & M-Banking)
                    </span>
                </div>
            </div>

            <!-- Step-by-Step Instructions -->
            <div class="text-left text-xs text-gray-600 bg-rose-50/40 p-4 rounded-2xl border border-rose-100 space-y-2">
                <span class="font-bold text-gray-900 block flex items-center gap-1.5">
                    <span>📱</span>
                    <span>Cara Bayar Menggunakan Aplikasi Favorit:</span>
                </span>
                <ol class="list-decimal list-inside space-y-1 text-gray-600 leading-relaxed font-light">
                    <li>Buka aplikasi <strong>DANA, GoPay, OVO, ShopeePay, BCA Mobile, Livin' Mandiri, BRImo</strong>, atau m-Banking Anda.</li>
                    <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                    <li>Arahkan kamera ke QR di atas atau pilih dari galeri foto.</li>
                    <li>Pastikan nama merchant: <strong>MocYy, Kerajinan</strong>.</li>
                    <li>Ketik nominal: <strong>@currency($order->grand_total)</strong> lalu konfirmasi bayar.</li>
                    <li>Ambil screenshot bukti transfer dan unggah di samping.</li>
                </ol>
            </div>

        </div>

        <!-- Right: Upload Proof Form (6 cols) -->
        <div class="md:col-span-6 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
            <div class="pb-4 border-b border-gray-100">
                <h2 class="font-serif text-lg font-bold text-gray-900">Formulir Bukti Pembayaran</h2>
                <p class="text-xs text-gray-500 mt-1">Unggah tangkapan layar (screenshot) transfer atau struk mutasi Anda di sini.</p>
            </div>

            <form action="{{ route('customer.orders.upload_proof', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- File Upload Input with Live Preview -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        File Bukti Transfer *
                    </label>
                    <div class="border-2 border-dashed border-gray-200 hover:border-[#D48C9A] rounded-2xl p-5 text-center transition-colors bg-gray-50/50">
                        
                        <!-- Thumbnail Preview if image selected -->
                        <template x-if="filePreview">
                            <div class="mb-3">
                                <img :src="filePreview" alt="Preview Bukti" class="max-h-48 mx-auto rounded-xl shadow-md border border-gray-200 object-contain">
                                <div class="mt-2 text-xs font-semibold text-gray-700 flex items-center justify-center gap-2">
                                    <span x-text="fileName"></span>
                                    <span class="text-gray-400" x-text="'(' + fileSize + ')'"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="!filePreview">
                            <div>
                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="text-xs text-gray-600 font-semibold">Pilih atau seret file bukti transfer Anda ke sini</p>
                            </div>
                        </template>

                        <input type="file" name="proof_file" required accept="image/jpeg,image/png,image/webp,application/pdf"
                            @change="handleFileSelect($event)"
                            class="block w-full text-xs text-gray-500 mt-3 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-[#8C534A] hover:file:bg-rose-100 cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-2">Format yang didukung: JPG, PNG, WebP, atau PDF (Maksimal 5MB)</p>
                    </div>
                    @error('proof_file')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sender Account Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Nama Pengirim / Pemilik Rekening Transfer
                    </label>
                    <input type="text" name="sender_account_name" value="{{ old('sender_account_name', $order->user->name) }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                        placeholder="Contoh: Siti Rahmawati">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="notes" rows="2"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"
                        placeholder="Contoh: Transfer via DANA / BCA jam 10:15 WIB"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-rose-200/60 hover:-translate-y-0.5 transition-all">
                        Unggah Bukti Pembayaran &rarr;
                    </button>
                </div>
            </form>

            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl text-[11px] text-amber-900 space-y-1">
                <span class="font-bold flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Verifikasi:
                </span>
                <p>Setelah bukti diunggah, pesanan akan otomatis berstatus <strong>Menunggu Verifikasi Admin</strong>. Admin MocYy akan mencocokkan mutasi sebelum buket dirangkai dan dikirim.</p>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs text-gray-500 hover:text-gray-800">
                    &larr; Kembali ke Rincian Pesanan
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
