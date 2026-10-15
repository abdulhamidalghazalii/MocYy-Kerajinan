<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp,pdf',
                'max:5120', // max 5MB
            ],
            'sender_account_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_file.required' => 'Pilih file bukti pembayaran untuk diunggah.',
            'proof_file.mimes' => 'Format file bukti harus berupa gambar (JPG, PNG, WebP) atau dokumen PDF.',
            'proof_file.max' => 'Ukuran maksimal file bukti pembayaran adalah 5MB.',
        ];
    }
}
