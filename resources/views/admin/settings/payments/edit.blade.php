@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran - ' . $paymentMethod->name)
@section('page_title', 'Edit / Ganti QR Metode Pembayaran: ' . $paymentMethod->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <a href="{{ route('admin.settings.payments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1.5">
            <span>&larr;</span>
            <span>Kembali ke Daftar Metode Pembayaran</span>
        </a>

        @if($paymentMethod->orders()->count() === 0)
            <form action="{{ route('admin.settings.payments.destroy', $paymentMethod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Metode
                </button>
            </form>
        @endif
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
        <form action="{{ route('admin.settings.payments.update', $paymentMethod->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Unik Sistem <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $paymentMethod->code) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono text-slate-900 lowercase">
                    <p class="text-[10px] text-slate-400 mt-1">Kode unik sistem (contoh: qris, bca, dana).</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pemilik Akun / Merchant</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $paymentMethod->account_name) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Akun / Rekening / NMID (Opsional)</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $paymentMethod->account_number) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono text-slate-900">
                </div>

                <!-- QR Image Preview & Replacement Field -->
                <div class="sm:col-span-2 p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div>
                        <label class="block font-bold text-slate-800 text-sm">Kode QR Pembayaran (Edit / Ganti QR)</label>
                        <p class="text-[11px] text-slate-500 mt-0.5">Unggah gambar baru untuk mengganti kode QR saat ini. Format JPG, PNG, atau WebP.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <!-- Current & New Preview -->
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-center">
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">QR Saat Ini</span>
                                <div class="w-28 h-28 rounded-2xl bg-white border border-slate-200 p-1.5 flex items-center justify-center shadow-2xs overflow-hidden">
                                    @if($paymentMethod->qr_image_url)
                                        <img src="{{ $paymentMethod->qr_image_url }}" alt="QR Aktif" class="w-full h-full object-contain rounded-xl">
                                    @else
                                        <span class="text-[10px] text-slate-400">Belum ada QR</span>
                                    @endif
                                </div>
                            </div>

                            <div id="new_qr_box" class="text-center hidden">
                                <span class="text-[10px] font-bold text-emerald-600 block mb-1">QR Baru (Preview)</span>
                                <div class="w-28 h-28 rounded-2xl bg-emerald-50 border-2 border-dashed border-emerald-400 p-1.5 flex items-center justify-center shadow-2xs overflow-hidden">
                                    <img id="new_qr_img" src="" alt="QR Baru" class="w-full h-full object-contain rounded-xl">
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-2">
                            <label class="block font-semibold text-slate-700">Pilih File Gambar QR Baru:</label>
                            <input type="file" id="edit_qr_file" name="qr_file" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <p class="text-[10px] text-slate-400">Kosongkan jika tidak ingin mengubah gambar QR Code yang sudah ada.</p>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Petunjuk Pembayaran Langkah-demi-Langkah</label>
                    <textarea name="instructions" rows="4"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none leading-relaxed text-slate-800">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Batas Waktu Bayar (Menit) <span class="text-rose-500">*</span></label>
                    <input type="number" name="expiry_minutes" value="{{ old('expiry_minutes', $paymentMethod->expiry_minutes) }}" required min="5" max="1440"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-900">
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-rose-500 focus:ring-rose-400">
                        <span class="font-bold text-slate-700">Aktifkan Metode Ini di Checkout Storefront</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="{{ route('admin.settings.payments.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan & Update QR
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('edit_qr_file');
        const newBox = document.getElementById('new_qr_box');
        const newImg = document.getElementById('new_qr_img');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        newImg.src = evt.target.result;
                        newBox.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    newBox.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
