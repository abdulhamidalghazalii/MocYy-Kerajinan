@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran & QR')
@section('page_title', 'Pengaturan Metode Pembayaran & Kode QR')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Metode Pembayaran Toko</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola gambar QR Code, nomor rekening/akun merchant, dan petunjuk transfer untuk customer saat checkout.</p>
        </div>
        <a href="{{ route('admin.settings.payments.create') }}" class="px-4 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition shadow-xs flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Tambah Metode Pembayaran</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($methods as $method)
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between space-y-4 transition hover:border-slate-300">
                <div class="space-y-4">
                    <div class="flex items-start justify-between gap-4 pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-base text-slate-900">{{ $method->name }}</h3>
                                <form action="{{ route('admin.settings.payments.toggle', $method->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold transition {{ $method->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}" title="Klik untuk mengubah status aktif/nonaktif">
                                        {{ $method->is_active ? '● Aktif' : '○ Nonaktif' }}
                                    </button>
                                </form>
                            </div>
                            <span class="font-mono text-xs text-slate-400">Kode: {{ $method->code }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.settings.payments.edit', $method->id) }}" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-[#782046] font-bold rounded-xl text-xs transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span>Edit / Ganti QR</span>
                            </a>
                        </div>
                    </div>

                    <!-- QR Image & Details -->
                    <div class="flex items-start gap-4">
                        <div class="w-28 h-28 rounded-2xl bg-slate-50 border border-slate-200 p-2 shrink-0 flex items-center justify-center overflow-hidden">
                            @if($method->qr_image_url)
                                <img src="{{ $method->qr_image_url }}" alt="QR Code" class="w-full h-full object-contain rounded-lg">
                            @else
                                <span class="text-[10px] text-slate-400 text-center font-medium">Belum ada QR</span>
                            @endif
                        </div>

                        <div class="space-y-2 text-xs text-slate-600 flex-1">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nama Akun / Merchant:</span>
                                <span class="font-bold text-slate-900">{{ $method->account_name ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nomor Akun / Rekening / NMID:</span>
                                <span class="font-mono font-bold text-slate-900 break-all">{{ $method->account_number ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Batas Waktu Bayar:</span>
                                <span class="font-semibold text-slate-800">{{ $method->expiry_minutes }} Menit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions preview -->
                    @if($method->instructions)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-[11px] text-slate-600 space-y-1">
                            <span class="font-bold text-slate-800 block">Petunjuk Pembayaran:</span>
                            <p class="whitespace-pre-line line-clamp-3 leading-relaxed">{{ $method->instructions }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                    <span>Diperbarui: {{ $method->updated_at ? $method->updated_at->format('d M Y, H:i') : '-' }}</span>
                    <a href="{{ route('admin.settings.payments.edit', $method->id) }}" class="font-bold text-rose-600 hover:text-rose-800 hover:underline flex items-center gap-1">
                        <span>Pengaturan Lengkap</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 p-12 text-center bg-white rounded-3xl border border-slate-200">
                <p class="text-sm font-semibold text-slate-600">Belum ada metode pembayaran yang terdaftar.</p>
                <a href="{{ route('admin.settings.payments.create') }}" class="mt-3 inline-block px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold">
                    + Tambah Metode Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
