<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class StoreApiController extends Controller
{
    /**
     * Get store settings & general information.
     */
    public function settings(): JsonResponse
    {
        $settings = [
            'store_name' => StoreSetting::get('store_name', config('store.name', 'MocYy')),
            'store_tagline' => StoreSetting::get('store_tagline', config('store.tagline', 'Buket Bunga & Hadiah Eksklusif')),
            'store_phone' => StoreSetting::get('store_phone', config('store.phone', '+62 813-5487-734')),
            'store_whatsapp' => StoreSetting::get('store_whatsapp', config('store.whatsapp', '628135487734')),
            'store_email' => StoreSetting::get('store_email', config('store.email', 'halo@mocyy.com')),
            'store_address' => StoreSetting::get('store_address', config('store.address', 'Jl. Senopati Indah No. 88, Kebayoran Baru, Jakarta Selatan 12190')),
            'store_instagram' => StoreSetting::get('store_instagram', config('store.instagram', '@mocyy.official')),
            'working_hours' => StoreSetting::get('working_hours', config('store.working_hours', 'Senin - Minggu: 08.00 - 21.00 WIB')),
        ];

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Get active shipping methods.
     */
    public function shippingMethods(): JsonResponse
    {
        $methods = ShippingMethod::where('is_active', true)
            ->get()
            ->map(fn($sm) => [
                'id' => $sm->id,
                'name' => $sm->name,
                'code' => $sm->code,
                'description' => $sm->description,
                'cost' => (float) $sm->cost,
                'estimated_time' => $sm->estimated_time,
            ]);

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }

    /**
     * Get active payment methods.
     */
    public function paymentMethods(): JsonResponse
    {
        $methods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($pm) => [
                'id' => $pm->id,
                'name' => $pm->name,
                'code' => $pm->code,
                'account_name' => $pm->account_name,
                'account_number' => $pm->account_number,
                'qr_image_url' => $pm->qr_image_url,
                'instructions' => $pm->instructions,
                'expiry_minutes' => $pm->expiry_minutes,
            ]);

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }
}
