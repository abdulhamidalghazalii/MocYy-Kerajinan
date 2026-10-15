@extends('layouts.admin')

@section('title', 'Edit Akun Customer')
@section('page_title', 'Ubah Data Akun & Email Pelanggan')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Customer</span>
        </a>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs">
        <div class="mb-6 pb-6 border-b border-slate-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-400 text-white font-bold flex items-center justify-center text-xl shadow-md">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ $customer->name }}</h3>
                <p class="text-xs text-slate-500 font-mono">ID: #{{ $customer->id }} &bull; Terdaftar: {{ $customer->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Lengkap Pelanggan
                </label>
                <input
                    type="text"
                    name="name"
                    required
                    value="{{ old('name', $customer->name) }}"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address (Customer Email Management) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Email Customer (Login & Notifikasi)
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    value="{{ old('email', $customer->email) }}"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                <p class="text-[11px] text-slate-400 mt-1">
                    Admin dapat memperbarui alamat email jika customer salah ketik atau meminta pergantian email resmi.
                </p>
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nomor WhatsApp / Telepon
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('phone')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reset Password (Optional) -->
            <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                    Reset Password Customer (Opsional)
                </label>
                <p class="text-[11px] text-amber-700">
                    Kosongkan kolom di bawah jika Anda tidak ingin mengubah password customer.
                </p>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password baru jika ingin mereset (min. 6 karakter)"
                    class="w-full px-4 py-2.5 rounded-xl border border-amber-300 bg-white text-sm focus:outline-hidden"
                >
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Status -->
            <div class="flex items-center gap-3 pt-2">
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $customer->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 rounded-sm border-slate-300 focus:ring-rose-500"
                >
                <label for="is_active" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Akun Aktif (Dapat melakukan login dan berbelanja)
                </label>
            </div>

            <!-- Submit CTA -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
