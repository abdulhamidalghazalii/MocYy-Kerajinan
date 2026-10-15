@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page_title', 'Pengaturan Profil Toko & Kontak')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h3 class="font-bold text-slate-800 text-base">Identitas & Informasi Operasional Florist</h3>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi ini ditampilkan pada header toko, kartu ucapan, dan tombol konsultasi WhatsApp.</p>
        </div>

        <form action="{{ route('admin.settings.store.update') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Toko *</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? config('store.name', 'MocYy')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tagline Slogan</label>
                    <input type="text" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline'] ?? config('store.tagline', 'Buket Bunga & Hadiah Eksklusif')) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp Resmi (Format: 628xxx) *</label>
                    <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp'] ?? config('store.whatsapp', '628135487734')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono text-slate-800">
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk direct chat konsultasi customer dan floating support.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Telepon Toko</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? config('store.phone', '+62 813-5487-734')) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Toko</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? config('store.email', 'halo@mocyy.com')) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Akun Instagram</label>
                    <input type="text" name="store_instagram" value="{{ old('store_instagram', $settings['store_instagram'] ?? config('store.instagram', '@mocyy.official')) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Workshop / Studio Florist</label>
                    <textarea name="store_address" rows="2"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">{{ old('store_address', $settings['store_address'] ?? 'Jl. Melati Indah No. 42, Kebayoran Baru, Jakarta Selatan 12150') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jam Operasional / Produksi</label>
                    <input type="text" name="working_hours" value="{{ old('working_hours', $settings['working_hours'] ?? 'Senin - Minggu (08:00 - 21:00 WIB)') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Default Batas Waktu Bayar (Menit)</label>
                    <input type="number" name="payment_expiry_minutes" value="{{ old('payment_expiry_minutes', $settings['payment_expiry_minutes'] ?? 120) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none text-slate-800">
                </div>
            </div>

            <div class="pt-5 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition-all">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
