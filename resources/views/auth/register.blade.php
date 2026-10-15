@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-[#F4EDE0]">
        
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-[#D48C9A] to-[#E07A5F] text-white shadow-md shadow-rose-200 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Buat Akun Customer</h2>
            <p class="mt-2 text-sm text-gray-500">Daftar untuk kemudahan memesan buket, tracking pengiriman, dan riwayat pesanan.</p>
        </div>

        <form class="mt-6 space-y-4" action="{{ route('register') }}" method="POST">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Lengkap</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                    class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('name') border-rose-400 @enderror"
                    placeholder="Contoh: Siti Rahmawati">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Alamat Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                    class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('email') border-rose-400 @enderror"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">No. WhatsApp / Handphone</label>
                <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                    class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('phone') border-rose-400 @enderror"
                    placeholder="081234567890">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kata Sandi</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('password') border-rose-400 @enderror"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Ulangi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm"
                    placeholder="Ulangi kata sandi">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] focus:ring-2 focus:ring-[#D48C9A] transition-all">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <div class="text-center pt-2">
            <p class="text-xs text-gray-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-[#D48C9A] hover:text-[#b06564] ml-1">Masuk ke akun</a>
            </p>
        </div>
    </div>
</div>
@endsection
