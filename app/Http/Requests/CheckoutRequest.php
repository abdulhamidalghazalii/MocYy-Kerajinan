<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:150'],
            'recipient_phone' => ['required', 'string', 'max:25'],
            'full_address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'greeting_card_note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_phone.required' => 'Nomor telepon penerima wajib diisi.',
            'full_address.required' => 'Alamat pengiriman lengkap wajib diisi.',
            'city.required' => 'Kota pengiriman wajib diisi.',
            'shipping_method_id.required' => 'Pilih opsi pengiriman.',
            'shipping_method_id.exists' => 'Metode pengiriman tidak valid.',
            'payment_method_id.required' => 'Pilih metode pembayaran (DANA / QRIS).',
            'payment_method_id.exists' => 'Metode pembayaran tidak valid.',
        ];
    }
}
