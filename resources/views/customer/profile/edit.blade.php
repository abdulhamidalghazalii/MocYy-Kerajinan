@extends('layouts.app')

@section('title', 'Profil & Alamat Saya | MocYy')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900">Profil & Buku Alamat</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data diri dan alamat pengiriman hadiah Anda untuk mempermudah pemesanan berikutnya.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Profile Info & Password (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
            <h2 class="font-serif text-lg font-bold text-gray-900 pb-3 border-b border-gray-100">
                Informasi Akun
            </h2>

            <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Alamat Email (Tetap)</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-xs text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">No. WhatsApp / HP</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                </div>

                <div class="pt-4 border-t border-gray-100 space-y-3">
                    <span class="text-xs font-bold text-gray-900 block">Ubah Kata Sandi (Kosongkan bila tidak ingin diubah)</span>
                    
                    <div>
                        <input type="password" name="current_password" placeholder="Kata sandi saat ini"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>
                    <div>
                        <input type="password" name="new_password" placeholder="Kata sandi baru (min 8 karakter)"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>
                    <div>
                        <input type="password" name="new_password_confirmation" placeholder="Ulangi kata sandi baru"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition-all">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Saved Addresses Management (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Buku Alamat Tersimpan ({{ $addresses->count() }})</h2>
                </div>

                @if($addresses->isEmpty())
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada alamat pengiriman yang tersimpan.</p>
                @else
                    <div class="space-y-4">
                        @foreach($addresses as $addr)
                            <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900">{{ $addr->label }}</span>
                                        @if($addr->is_default)
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Utama</span>
                                        @endif
                                    </div>
                                    <p class="font-semibold text-gray-800">{{ $addr->recipient_name }} • {{ $addr->recipient_phone }}</p>
                                    <p class="text-gray-500 font-light leading-relaxed">{{ $addr->full_address }}, {{ $addr->city }} {{ $addr->postal_code }}</p>
                                    @if($addr->notes)
                                        <p class="text-gray-400 italic">"{{ $addr->notes }}"</p>
                                    @endif
                                </div>

                                <form action="{{ route('customer.address.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs font-semibold">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Add New Address Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#F4EDE0] shadow-sm space-y-4">
                <h3 class="font-serif text-base font-bold text-gray-900 pb-2 border-b border-gray-100">
                    + Tambah Alamat Pengiriman Baru
                </h3>

                <form action="{{ route('customer.address.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Label Alamat (Rumah/Kantor/Kost)</label>
                        <input type="text" name="label" required placeholder="Contoh: Rumah"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" name="recipient_name" required placeholder="Nama penerima"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">No. WhatsApp Penerima</label>
                        <input type="tel" name="recipient_phone" required placeholder="081234567890"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kota / Kabupaten</label>
                        <input type="text" name="city" required placeholder="Jakarta Selatan"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="full_address" rows="2" required placeholder="Nama jalan, nomor rumah, RT/RW, gedung, patokan..."
                            class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kode Pos (Opsional)</label>
                        <input type="text" name="postal_code" placeholder="12150"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Catatan Tambahan (Patokan)</label>
                        <input type="text" name="notes" placeholder="Pagar putih sebelah minimarket"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#D48C9A] focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                            <input type="checkbox" name="is_default" value="1" class="rounded-sm border-gray-300 text-[#D48C9A] focus:ring-[#D48C9A]">
                            <span>Jadikan alamat utama pengiriman saya</span>
                        </label>
                    </div>

                    <div class="sm:col-span-2 pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#D48C9A] hover:bg-[#c67f7e] text-white font-bold rounded-xl transition-all shadow-xs">
                            Simpan Alamat Baru
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
