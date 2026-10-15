<?php

namespace App\Http\Requests\Admin;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $payment = $this->route('paymentMethod') ?? $this->route('payment') ?? $this->route('payment_method');
        $id = $payment instanceof PaymentMethod ? $payment->id : (is_numeric($payment) ? (int) $payment : null);

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                $id ? Rule::unique('payment_methods', 'code')->ignore($id) : Rule::unique('payment_methods', 'code'),
            ],
            'account_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'instructions' => ['nullable', 'string'],
            'expiry_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'is_active' => ['nullable', 'boolean'],
            'qr_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }
}
