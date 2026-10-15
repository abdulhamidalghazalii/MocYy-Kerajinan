<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku,' . $productId],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight_grams' => ['required', 'numeric', 'min:0'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'allows_greeting_card' => ['nullable', 'boolean'],
            'allows_custom_note' => ['nullable', 'boolean'],
            'main_image_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Pilih kategori produk.',
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'discount_price.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'stock.required' => 'Stok produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
        ];
    }
}
