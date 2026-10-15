<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Models\AdminActivityLog;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.settings.payments.index', compact('methods'));
    }

    public function create(): View
    {
        return view('admin.settings.payments.create');
    }

    public function store(PaymentMethodRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = strtolower(trim($validated['code']));

        if ($request->hasFile('qr_file')) {
            $validated['qr_image_path'] = $this->handleQrUpload($request->file('qr_file'), $validated['code']);
        }

        $method = PaymentMethod::create($validated);

        AdminActivityLog::record(
            Auth::id() ?? 1,
            'payment_method_created',
            PaymentMethod::class,
            $method->id,
            ['name' => $method->name, 'code' => $method->code]
        );

        return redirect()->route('admin.settings.payments.index')
            ->with('success', "Metode pembayaran '{$method->name}' berhasil ditambahkan.");
    }

    public function edit(PaymentMethod|int|string $paymentMethod): View
    {
        $paymentMethod = $paymentMethod instanceof PaymentMethod
            ? $paymentMethod
            : PaymentMethod::findOrFail($paymentMethod);

        return view('admin.settings.payments.edit', compact('paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, PaymentMethod|int|string $paymentMethod): RedirectResponse
    {
        $paymentMethod = $paymentMethod instanceof PaymentMethod
            ? $paymentMethod
            : PaymentMethod::findOrFail($paymentMethod);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['code'] = strtolower(trim($validated['code']));

        if ($request->hasFile('qr_file')) {
            // Delete old custom file if exists and not remote/seed
            if ($paymentMethod->qr_image_path && !str_starts_with($paymentMethod->qr_image_path, 'http')) {
                try {
                    Storage::disk('public')->delete($paymentMethod->qr_image_path);
                } catch (\Throwable $e) {
                    // Ignore delete failure
                }
            }

            $validated['qr_image_path'] = $this->handleQrUpload($request->file('qr_file'), $validated['code']);
        }

        $paymentMethod->update($validated);

        AdminActivityLog::record(
            Auth::id() ?? 1,
            'payment_method_updated',
            PaymentMethod::class,
            $paymentMethod->id,
            ['name' => $paymentMethod->name, 'code' => $paymentMethod->code]
        );

        return redirect()->route('admin.settings.payments.index')
            ->with('success', "Pengaturan metode pembayaran '{$paymentMethod->name}' dan QR Code berhasil diperbarui.");
    }

    public function toggleStatus(PaymentMethod|int|string $paymentMethod): RedirectResponse
    {
        $paymentMethod = $paymentMethod instanceof PaymentMethod
            ? $paymentMethod
            : PaymentMethod::findOrFail($paymentMethod);

        $paymentMethod->is_active = !$paymentMethod->is_active;
        $paymentMethod->save();

        $statusText = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AdminActivityLog::record(
            Auth::id() ?? 1,
            'payment_method_status_toggled',
            PaymentMethod::class,
            $paymentMethod->id,
            ['is_active' => $paymentMethod->is_active]
        );

        return back()->with('success', "Status metode pembayaran '{$paymentMethod->name}' berhasil {$statusText}.");
    }

    public function destroy(PaymentMethod|int|string $paymentMethod): RedirectResponse
    {
        $paymentMethod = $paymentMethod instanceof PaymentMethod
            ? $paymentMethod
            : PaymentMethod::findOrFail($paymentMethod);

        if ($paymentMethod->orders()->count() > 0) {
            return back()->with('error', "Metode pembayaran '{$paymentMethod->name}' tidak dapat dihapus karena sudah memiliki riwayat pesanan. Anda dapat menonaktifkannya.");
        }

        $name = $paymentMethod->name;
        $paymentMethod->delete();

        AdminActivityLog::record(
            Auth::id() ?? 1,
            'payment_method_deleted',
            PaymentMethod::class,
            null,
            ['name' => $name]
        );

        return redirect()->route('admin.settings.payments.index')
            ->with('success', "Metode pembayaran '{$name}' berhasil dihapus.");
    }

    /**
     * Upload QR image and synchronize across public asset directories.
     */
    protected function handleQrUpload(UploadedFile $file, ?string $code = null): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'qr_' . time() . '_' . Str::random(8) . '.' . $extension;
        $path = $file->storeAs('qr_codes', $filename, 'public');

        try {
            // 1. Mirror to Laravel public/images
            $publicImagesDir = public_path('images');
            if (!file_exists($publicImagesDir)) {
                mkdir($publicImagesDir, 0755, true);
            }
            copy($file->getRealPath(), $publicImagesDir . '/' . $filename);

            // If QRIS, also update default QRIS image targets
            if (strtolower($code ?? '') === 'qris') {
                copy($file->getRealPath(), $publicImagesDir . '/qris-mocyan.jpg');
                copy($file->getRealPath(), $publicImagesDir . '/qris.jpg');
                copy($file->getRealPath(), $publicImagesDir . '/qris_mocyy.jpg');
            }

            // 2. Mirror to Next.js frontend/public/images
            $frontendDir = base_path('frontend/public/images');
            if (file_exists($frontendDir)) {
                copy($file->getRealPath(), $frontendDir . '/' . $filename);
                if (strtolower($code ?? '') === 'qris') {
                    copy($file->getRealPath(), $frontendDir . '/qris-mocyan.jpg');
                    copy($file->getRealPath(), $frontendDir . '/qris.jpg');
                }
            }
        } catch (\Throwable $e) {
            Log::warning('QR image mirror error: ' . $e->getMessage());
        }

        return $path;
    }
}
