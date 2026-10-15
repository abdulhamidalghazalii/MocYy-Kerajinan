@extends('layouts.admin')

@section('title', 'Edit Buket: ' . $product->name)
@section('page_title', 'Edit Buket: ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6"
    x-data="{
        variants: {{ json_encode($product->variants->map(fn($v) => [
            'variant_name' => $v->variant_name,
            'variant_value' => $v->variant_value,
            'additional_price' => (float)$v->additional_price,
            'stock' => (int)$v->stock,
        ])) }},
        addVariant() {
            this.variants.push({ variant_name: 'Ukuran', variant_value: '', additional_price: 0, stock: 10 });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        }
    }">

    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Daftar Buket
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- 1. Informasi Utama -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Buket *</label>
                    <select name="category_id" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">SKU Produk</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-mono">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Buket / Hadiah *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Lengkap *</label>
                    <textarea name="description" rows="4" required
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Harga, Stok & Berat -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">2. Harga, Stok & Waktu Pengerjaan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Normal (IDR) *</label>
                    <input type="number" step="1000" name="price" value="{{ old('price', (int)$product->price) }}" required
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Diskon (Opsional)</label>
                    <input type="number" step="1000" name="discount_price" value="{{ old('discount_price', $product->discount_price ? (int)$product->discount_price : '') }}"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jumlah Stok Utama *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Berat Produk (Gram) *</label>
                    <input type="number" name="weight_grams" value="{{ old('weight_grams', (int)$product->weight_grams) }}" required min="50"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Estimasi Hari Pengerjaan *</label>
                    <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $product->lead_time_days) }}" required min="1" max="14"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Foto Produk -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">3. Foto Buket</h2>

            <div class="flex items-center gap-4">
                <img src="{{ $product->main_image_url }}" alt="Preview" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                <div class="flex-1 text-xs space-y-1">
                    <input type="file" name="main_image_file" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <p class="text-[11px] text-slate-400">Pilih file baru jika ingin mengganti foto utama.</p>
                </div>
            </div>
        </div>

        <!-- 4. Variasi Produk -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-base text-slate-900">4. Variasi Produk</h2>
                    <p class="text-xs text-slate-400">Kelola opsi ukuran atau warna wrapping.</p>
                </div>
                <button type="button" @click="addVariant()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200">
                    + Tambah Baris Variasi
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <div class="sm:col-span-3">
                            <input type="text" :name="'variants[' + index + '][variant_name]'" x-model="v.variant_name" placeholder="Jenis"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-3">
                            <input type="text" :name="'variants[' + index + '][variant_value]'" x-model="v.variant_value" placeholder="Pilihan"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-3">
                            <input type="number" :name="'variants[' + index + '][additional_price]'" x-model="v.additional_price" placeholder="+Rp"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" :name="'variants[' + index + '][stock]'" x-model="v.stock" placeholder="Stok"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-1 text-right">
                            <button type="button" @click="removeVariant(index)" class="p-2 text-rose-500 hover:text-rose-700">
                                &times;
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 5. Status & Fitur -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">5. Pengaturan Tampilan & Fitur</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Aktifkan di Katalog</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Jadikan Produk Unggulan</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="allows_greeting_card" value="1" {{ $product->allows_greeting_card ? 'checked' : '' }} class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Sediakan Kartu Ucapan Gratis</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="allows_custom_note" value="1" {{ $product->allows_custom_note ? 'checked' : '' }} class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Izinkan Catatan Khusus</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs">
                Batal
            </a>
            <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-md">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
