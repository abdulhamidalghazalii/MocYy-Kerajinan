@extends('layouts.admin')

@section('title', 'Manajemen Produk Buket')
@section('page_title', 'Katalog Produk Buket & Hadiah')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-3 flex-1 max-w-lg text-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama buket, SKU..."
                class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
            
            <select name="category_id" onchange="this.form.submit()" class="py-2.5 px-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800">
                Cari
            </button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-[#D48C9A] to-[#E07A5F] hover:from-[#c67f7e] hover:to-[#d06b50] text-white font-bold text-xs rounded-xl shadow-xs self-start sm:self-auto flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Tambah Buket Baru</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center text-slate-400 text-sm">
                Tidak ada produk ditemukan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Buket / Foto</th>
                            <th class="py-3.5 px-4">Kategori & SKU</th>
                            <th class="py-3.5 px-4">Harga Normal</th>
                            <th class="py-3.5 px-4">Harga Diskon</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Variasi</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($products as $prod)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- Photo & Name -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                            <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-rose-600 block line-clamp-1 max-w-[200px]">
                                                {{ $prod->name }}
                                            </a>
                                            @if($prod->is_featured)
                                                <span class="inline-block px-1.5 py-0.2 text-[9px] font-bold rounded-sm bg-amber-100 text-amber-800">Unggulan</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & SKU -->
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $prod->category?->name }}</span>
                                    <span class="font-mono text-[11px] text-slate-400">{{ $prod->sku }}</span>
                                </td>

                                <!-- Price -->
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    @currency($prod->price)
                                </td>

                                <!-- Discount Price -->
                                <td class="py-3.5 px-4">
                                    @if($prod->discount_price)
                                        <span class="font-bold text-emerald-600">@currency($prod->discount_price)</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Stock -->
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $prod->stock > 5 ? 'bg-slate-100 text-slate-700' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $prod->stock }} unit
                                    </span>
                                </td>

                                <!-- Variants Count -->
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-500">{{ $prod->variants->count() }} variasi</span>
                                </td>

                                <!-- Active -->
                                <td class="py-3.5 px-4">
                                    @if($prod->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">Nonaktif</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.products.edit', $prod->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
