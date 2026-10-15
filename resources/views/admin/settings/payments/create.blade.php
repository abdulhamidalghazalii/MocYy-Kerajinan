@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran')
@section('page_title', 'Tambah Metode Pembayaran Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <a href="{{ route('admin.settings.payments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1.5">
            <span>&larr;</span>
            <span>Kembali ke Daftar Metode Pembayaran</span>
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <span class="font-bold block">Terdapat kesalahan pengisian data:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.settings.payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" id="method_name" name="name" value="{{ old('name') }}" required placeholder="Contoh: QRIS Merchant MocYy / Transfer BCA"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Unik Sistem <span class="text-rose-500">*</span></label>
                    <input type="text" id="method_code" name="code" value="{{ old('code') }}" required placeholder="qris / bca / dana / mandiri"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono text-slate-900 lowercase">
                    <p class="text-[10px] text-slate-400 mt-1">Gunakan huruf kecil tanpa spasi (misal: qris, bca, dana).</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pemilik Akun / Merchant</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="Contoh: MocYy, Kerajinan"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Akun / Rekening / NMID (Opsional)</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Contoh: NMID: ID1026613806474 / 8881234567"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono text-slate-900">
                </div>

                <!-- QR Image Upload with Live Preview -->
                <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1">Upload Gambar QR Code Pembayaran (JPG, PNG, WebP)</label>
                        <p class="text-[11px] text-slate-500">Pilih gambar kode QR jika metode pembayaran ini menggunakan barcode QRIS atau QR e-Wallet.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div id="qr_preview_container" class="w-32 h-32 rounded-xl bg-white border border-slate-200 p-2 shrink-0 flex items-center justify-center overflow-hidden shadow-2xs">
                            <img id="qr_preview_img" src="" alt="Preview QR" class="w-full h-full object-contain rounded-lg hidden">
                            <span id="qr_preview_placeholder" class="text-[10px] text-slate-400 text-center font-medium">Belum ada file dipilih</span>
                        </div>

                        <div class="flex-1 w-full space-y-2">
                            <input type="file" id="qr_file_input" name="qr_file" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <p class="text-[10px] text-slate-400">Maksimum ukuran file: 5 MB. Gambar akan disinkronkan otomatis ke storefront.</p>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Petunjuk Pembayaran Langkah-demi-Langkah</label>
                    <textarea name="instructions" rows="4" placeholder="1. Buka aplikasi m-Banking atau e-Wallet&#10;2. Scan kode QR...&#10;3. Selesaikan transfer..."
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none leading-relaxed text-slate-800">{{ old('instructions') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Batas Waktu Bayar (Menit) <span class="text-rose-500">*</span></label>
                    <input type="number" name="expiry_minutes" value="{{ old('expiry_minutes', 120) }}" required min="5" max="1440"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-900">
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-rose-500 focus:ring-rose-400">
                        <span class="font-bold text-slate-700">Aktifkan Metode Ini di Checkout Storefront</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.settings.payments.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition">
                    Simpan Metode Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('qr_file_input');
        const previewImg = document.getElementById('qr_preview_img');
        const placeholder = document.getElementById('qr_preview_placeholder');
        const nameInput = document.getElementById('method_name');
        const codeInput = document.getElementById('method_code');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        previewImg.src = evt.target.result;
                        previewImg.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewImg.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            });
        }

        if (nameInput && codeInput) {
            nameInput.addEventListener('blur', function () {
                if (!codeInput.value) {
                    codeInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').substring(0, 30);
                }
            });
        }
    });
</script>
@endsection
