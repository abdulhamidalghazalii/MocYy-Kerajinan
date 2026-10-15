<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(int $orderId): View
    {
        $order = Order::with(['paymentMethod', 'latestPayment.proofs'])->findOrFail($orderId);

        if (!Gate::allows('view-order', $order)) {
            abort(403, 'Akses ditolak.');
        }

        return view('customer.orders.payment', compact('order'));
    }

    public function uploadProof(UploadPaymentProofRequest $request, int $orderId): RedirectResponse
    {
        $order = Order::findOrFail($orderId);

        if (!Gate::allows('view-order', $order)) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $this->paymentService->uploadProof(
                $order,
                $request->file('proof_file'),
                $request->sender_account_name,
                $request->notes
            );

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Bukti pembayaran berhasil diunggah! Status pembayaran kini dalam pemeriksaan admin.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengunggah bukti: ' . $e->getMessage());
        }
    }

    public function downloadProof(int $proofId): StreamedResponse|BinaryFileResponse
    {
        $proof = PaymentProof::with('payment.order')->findOrFail($proofId);

        return $this->paymentService->serveProof($proof, Auth::user());
    }
}
