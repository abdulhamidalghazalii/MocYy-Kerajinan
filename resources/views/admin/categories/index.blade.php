@extends('layouts.admin')

@section('title', 'Kategori Buket')
@section('page_title', 'Kategori Buket & Hadiah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Kelola kategori untuk pengelompokan produk di storefront.</p>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-xl text-xs hover:bg-slate-800 transition-colors">
            + Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-4">Urutan</th>
                    <th class="py-3.5 px-4">Kategori / Ikon</th>
                    <th class="py-3.5 px-4">Slug</th>
                    <th class="py-3.5 px-4">Deskripsi</th>
                    <th class="py-3.5 px-4">Jumlah Produk</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @foreach($categories as $cat)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3.5 px-4 font-bold text-slate-400">
                            {{ $cat->sort_order }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $cat->image ?: 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=100&q=80' }}" alt="{{ $cat->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                <span class="font-bold text-slate-900">{{ $cat->name }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                            {{ $cat->slug }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate">
                            {{ $cat->description }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            {{ $cat->products_count }} produk
                        </td>
                        <td class="py-3.5 px-4">
                            @if($cat->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-rose-500 hover:text-rose-700">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
