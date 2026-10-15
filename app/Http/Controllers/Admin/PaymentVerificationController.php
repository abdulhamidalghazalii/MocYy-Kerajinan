<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentVerificationController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending_review');

        $query = Order::with(['user', 'paymentMethod', 'latestPayment.proofs'])
            ->whereHas('payments');

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                  ->orWhere('recipient_name', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%")
                         ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest('updated_at')->paginate(15)->withQueryString();

        $counts = [
            'pending_review' => Order::where('payment_status', 'pending_review')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),
            'rejected' => Order::where('payment_status', 'rejected')->count(),
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
        ];

        return view('admin.payments.verification', compact('orders', 'status', 'counts'));
    }

    public function show(int $orderId): View
    {
        $order = Order::with([
            'user',
            'paymentMethod',
            'items',
            'payments.proofs',
            'payments.verifier',
            'statusHistories.actor',
        ])->findOrFail($orderId);

        return view('admin.payments.show', compact('order'));
    }

    public function confirm(Request $request, int $orderId): RedirectResponse
    {
        $order = Order::findOrFail($orderId);
        $transactionRef = $request->input('transaction_ref');

        try {
            $this->paymentService->confirmPayment($order, Auth::user(), $transactionRef);

            return redirect()->route('admin.payments.verification')
                ->with('success', "Pembayaran untuk pesanan {$order->order_number} berhasil dikonfirmasi sah. Status pesanan diperbarui ke Diproses.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function reject(RejectPaymentRequest $request, int $orderId): RedirectResponse
    {
        $order = Order::findOrFail($orderId);

        try {
            $this->paymentService->rejectPayment($order, Auth::user(), $request->rejection_reason);

            return redirect()->route('admin.payments.verification')
                ->with('success', "Pembayaran untuk pesanan {$order->order_number} telah ditolak dengan alasan yang tercatat.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    public function viewProof(int $proofId): StreamedResponse|BinaryFileResponse
    {
        $proof = PaymentProof::with('payment.order')->findOrFail($proofId);

        return $this->paymentService->serveProof($proof, Auth::user());
    }
}
