<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function index(): View
    {
        $settings = StoreSetting::all()->pluck('value', 'key');
        return view('admin.settings.store', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $allowedKeys = [
            'store_name',
            'store_tagline',
            'store_phone',
            'store_whatsapp',
            'store_email',
            'store_address',
            'store_instagram',
            'working_hours',
            'payment_expiry_minutes',
        ];

        foreach ($allowedKeys as $key) {
            if ($request->has($key)) {
                StoreSetting::set($key, $request->input($key));
            }
        }

        AdminActivityLog::record(
            Auth::id(),
            'store_settings_updated',
            null,
            null,
            ['updated_by' => Auth::user()->name]
        );

        return back()->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
