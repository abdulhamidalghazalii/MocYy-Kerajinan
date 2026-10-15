@extends('layouts.admin')

@section('title', 'Tambah Produk Buket Baru')
@section('page_title', 'Tambah Produk Buket Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6"
    x-data="{
        variants: [
            { variant_name: 'Ukuran', variant_value: 'Standard', additional_price: 0, stock: 10 }
        ],
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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. Informasi Utama -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Buket *</label>
                    <select name="category_id" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">SKU Produk (Opsional)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Otomatis jika kosong"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Buket / Hadiah *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Dusty Rose Romance Bouquet"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none font-semibold">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Lengkap *</label>
                    <textarea name="description" rows="4" required placeholder="Tuliskan komposisi bunga/snack, jenis pita, keistimewaan buket..."
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Harga, Stok & Berat -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">2. Harga, Stok & Waktu Pengerjaan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Normal (IDR) *</label>
                    <input type="number" step="1000" name="price" value="{{ old('price') }}" required placeholder="250000"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Diskon (Opsional)</label>
                    <input type="number" step="1000" name="discount_price" value="{{ old('discount_price') }}" placeholder="220000"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jumlah Stok Utama *</label>
                    <input type="number" name="stock" value="{{ old('stock', 20) }}" required min="0"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Berat Produk (Gram) *</label>
                    <input type="number" name="weight_grams" value="{{ old('weight_grams', 500) }}" required min="50"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Estimasi Hari Pengerjaan *</label>
                    <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 1) }}" required min="1" max="14"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Foto Produk -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">3. Foto Buket Utama</h2>

            <div class="text-xs space-y-2">
                <input type="file" name="main_image_file" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                <p class="text-[11px] text-slate-400">Rekomendasi rasio 1:1 atau 4:3, format JPG/PNG/WebP maks 4MB.</p>
            </div>
        </div>

        <!-- 4. Variasi Produk Dinamis (Alpine.js) -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h2 class="font-bold text-base text-slate-900">4. Variasi Produk (Ukuran, Warna Kertas, dll.)</h2>
                    <p class="text-xs text-slate-400">Tambahkan opsi ukuran atau nuansa warna yang bisa dipilih customer.</p>
                </div>
                <button type="button" @click="addVariant()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200">
                    + Tambah Baris Variasi
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <div class="sm:col-span-3">
                            <input type="text" :name="'variants[' + index + '][variant_name]'" x-model="v.variant_name" placeholder="Jenis (e.g. Ukuran)"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-3">
                            <input type="text" :name="'variants[' + index + '][variant_value]'" x-model="v.variant_value" placeholder="Pilihan (e.g. Large 20 Tangkai)"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-rose-400">
                        </div>
                        <div class="sm:col-span-3">
                            <input type="number" :name="'variants[' + index + '][additional_price]'" x-model="v.additional_price" placeholder="Biaya Tambahan (+Rp)"
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

        <!-- 5. Opsi Tambahan & Status -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h2 class="font-bold text-base text-slate-900 pb-2 border-b border-slate-100">5. Pengaturan Tampilan & Fitur</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Aktifkan di Katalog</span>
                        <span class="text-[11px] text-slate-400">Produk langsung dapat dilihat & dibeli</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Jadikan Produk Unggulan</span>
                        <span class="text-[11px] text-slate-400">Tampil di halaman depan (Home)</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="allows_greeting_card" value="1" checked class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Sediakan Kartu Ucapan Gratis</span>
                        <span class="text-[11px] text-slate-400">Customer dapat mengetikkan pesan</span>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                    <input type="checkbox" name="allows_custom_note" value="1" checked class="rounded-sm border-slate-300 text-rose-500 focus:ring-rose-400">
                    <div>
                        <span class="font-bold text-slate-800 block">Izinkan Catatan Khusus</span>
                        <span class="text-[11px] text-slate-400">Catatan warna pita atau jam kirim</span>
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
                Simpan & Publikasikan Buket
            </button>
        </div>

    </form>
</div>
@endsection
