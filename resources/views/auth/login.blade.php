@extends('layouts.app')

@section('title', 'Masuk ke Akun Anda')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-[#F4EDE0]">
        
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-[#D48C9A] to-[#E07A5F] text-white shadow-md shadow-rose-200 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Selamat Datang</h2>
            <p class="mt-2 text-sm text-gray-500">Masuk untuk melihat riwayat pesanan & upload bukti pembayaran.</p>
        </div>


        <form class="mt-6 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Alamat Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}"
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('email') border-rose-400 @enderror"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kata Sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="appearance-none block w-full px-4 py-3 border border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#D48C9A] focus:border-transparent text-sm @error('password') border-rose-400 @enderror"
                    placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                    <input type="checkbox" name="remember" class="rounded-sm border-gray-300 text-[#D48C9A] focus:ring-[#D48C9A]">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D48C9A] transition-all duration-200">
                    Masuk ke Akun
                </button>
            </div>
        </form>

        <div class="text-center pt-2">
            <p class="text-xs text-gray-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-[#D48C9A] hover:text-[#b06564] ml-1">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
