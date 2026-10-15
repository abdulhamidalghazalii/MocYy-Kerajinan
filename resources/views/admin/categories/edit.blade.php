@extends('layouts.admin')

@section('title', 'Edit Kategori ' . $category->name)
@section('page_title', 'Edit Kategori: ' . $category->name)

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">
            &larr; Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Slug URL</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="2"
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Foto / Ikon Kategori</label>
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ $category->image ?: 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=100&q=80' }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                    <input type="file" name="image_file" accept="image/*"
                        class="block w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Urutan Tampilan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                        <span class="font-bold text-slate-700">Status Aktif</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
