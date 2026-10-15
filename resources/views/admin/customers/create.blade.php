@extends('layouts.admin')

@section('title', 'Tambah Customer Baru')
@section('page_title', 'Registrasi Akun Customer Baru')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Customer</span>
        </a>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 mb-6 pb-4 border-b border-slate-100">
            Formulir Akun Pelanggan Baru
        </h3>

        <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Lengkap Pelanggan *
                </label>
                <input
                    type="text"
                    name="name"
                    required
                    value="{{ old('name') }}"
                    placeholder="Contoh: Siti Rahmawati"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Email Customer *
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nomor WhatsApp / Telepon
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="Contoh: 085711223344"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('phone')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Password Akun *
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Minimal 6 karakter"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                >
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3 pt-2">
                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    checked
                    class="w-4 h-4 text-rose-600 rounded-sm border-slate-300 focus:ring-rose-500"
                >
                <label for="is_active" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Aktifkan akun segera
                </label>
            </div>

            <!-- Submit CTA -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.customers.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-md transition">
                    Buat Akun Customer
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
