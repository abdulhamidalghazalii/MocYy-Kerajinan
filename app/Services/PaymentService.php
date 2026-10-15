<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentService
{
    /**
     * Customer uploads payment proof.
     */
    public function uploadProof(
        Order $order,
        UploadedFile $file,
        ?string $senderName = null,
        ?string $notes = null
    ): PaymentProof {
        // Validation check
        if (!$order->canUploadPaymentProof()) {
            throw new Exception('Pesanan ini saat ini tidak menerima unggahan bukti pembayaran baru.');
        }

        // Validate MIME type & file extension
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new Exception('Format file tidak didukung. Harap unggah gambar (JPG, PNG, WebP) atau file PDF.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new Exception('Ukuran file maksimal adalah 5MB.');
        }

        return DB::transaction(function () use ($order, $file, $senderName, $notes) {
            // Find or initialize payment
            $payment = Payment::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method_id' => $order->payment_method_id,
                    'amount' => $order->grand_total,
                    'status' => 'unpaid',
                ]
            );

            // Store securely in private disk
            $filename = 'proof_' . $order->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_proofs/' . $order->id, $filename, 'private');

            $proof = PaymentProof::create([
                'payment_id' => $payment->id,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'sender_account_name' => $senderName,
                'notes' => $notes,
                'uploaded_at' => now(),
            ]);

            // Update statuses to pending_review
            $previousPaymentStatus = $payment->status;
            $payment->update([
                'status' => 'pending_review',
                'rejection_reason' => null, // reset rejection if any
            ]);

            $order->update([
                'payment_status' => 'pending_review',
            ]);

            // Track history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'type' => 'payment',
                'previous_status' => $previousPaymentStatus,
                'new_status' => 'pending_review',
                'notes' => "Bukti pembayaran diunggah oleh customer ({$proof->original_filename}). Menunggu verifikasi admin.",
            ]);

            // Notify Customer
            InAppNotification::send(
                $order->user_id,
                'Bukti Pembayaran Diterima',
                "Bukti pembayaran untuk pesanan {$order->order_number} berhasil diunggah dan sedang diperiksa oleh tim admin.",
                'info',
                route('customer.orders.show', $order->id)
            );

            return $proof;
        });
    }

    /**
     * Admin confirms manual payment.
     */
    public function confirmPayment(Order $order, User $admin, ?string $transactionRef = null): void
    {
        DB::transaction(function () use ($order, $admin, $transactionRef) {
            // Pessimistic lock on Order & Payment
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            $payment = Payment::where('order_id', $lockedOrder->id)->lockForUpdate()->first();

            if (!$payment) {
                throw new Exception('Data pembayaran tidak ditemukan untuk pesanan ini.');
            }

            // Anti double-confirmation check
            if ($payment->status === 'paid' || $lockedOrder->payment_status === 'paid') {
                throw new Exception('Pembayaran ini sudah pernah dikonfirmasi sebelumnya.');
            }

            if ($payment->status !== 'pending_review') {
                throw new Exception("Pembayaran tidak dapat dikonfirmasi karena status saat ini adalah '{$payment->status}'. Status harus 'pending_review'.");
            }

            // 1. Update Payment
            $payment->update([
                'status' => 'paid',
                'transaction_ref' => $transactionRef,
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            // 2. Update Order
            $previousOrderStatus = $lockedOrder->order_status;
            $lockedOrder->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'paid_at' => now(),
            ]);

            // 3. Track History
            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'user_id' => $admin->id,
                'type' => 'payment',
                'previous_status' => 'pending_review',
                'new_status' => 'paid',
                'notes' => "Pembayaran dikonfirmasi sah oleh {$admin->name}. Ref transaksi: " . ($transactionRef ?: '-'),
            ]);

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'user_id' => $admin->id,
                'type' => 'order',
                'previous_status' => $previousOrderStatus,
                'new_status' => 'processing',
                'notes' => 'Status pesanan otomatis beralih ke Diproses (Processing).',
            ]);

            // 4. Record Admin Activity Log
            AdminActivityLog::record(
                $admin->id,
                'payment_confirmed',
                Order::class,
                $lockedOrder->id,
                [
                    'order_number' => $lockedOrder->order_number,
                    'amount' => $payment->amount,
                    'transaction_ref' => $transactionRef,
                ]
            );

            // 5. In-App Notification for Customer
            InAppNotification::send(
                $lockedOrder->user_id,
                'Pembayaran Dikonfirmasi! 🎉',
                "Pembayaran pesanan {$lockedOrder->order_number} telah dikonfirmasi oleh Admin. Tim florist kami segera memproses rangkaian pesanan Anda.",
                'success',
                route('customer.orders.show', $lockedOrder->id)
            );
        });
    }

    /**
     * Admin rejects payment with mandatory reason.
     */
    public function rejectPayment(Order $order, User $admin, string $reason): void
    {
        $trimmedReason = trim($reason);
        if (empty($trimmedReason)) {
            throw new Exception('Alasan penolakan pembayaran wajib diisi.');
        }

        DB::transaction(function () use ($order, $admin, $trimmedReason) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            $payment = Payment::where('order_id', $lockedOrder->id)->lockForUpdate()->first();

            if (!$payment) {
                throw new Exception('Data pembayaran tidak ditemukan.');
            }

            if ($payment->status === 'paid') {
                throw new Exception('Pembayaran yang sudah berstatus LUNAS tidak dapat ditolak secara langsung.');
            }

            $payment->update([
                'status' => 'rejected',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => $trimmedReason,
            ]);

            $lockedOrder->update([
                'payment_status' => 'rejected',
            ]);

            // Track History (Keep existing proofs intact!)
            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'user_id' => $admin->id,
                'type' => 'payment',
                'previous_status' => 'pending_review',
                'new_status' => 'rejected',
                'notes' => "Pembayaran ditolak oleh {$admin->name}. Alasan: {$trimmedReason}",
            ]);

            // Record Admin Activity Log
            AdminActivityLog::record(
                $admin->id,
                'payment_rejected',
                Order::class,
                $lockedOrder->id,
                [
                    'order_number' => $lockedOrder->order_number,
                    'reason' => $trimmedReason,
                ]
            );

            // In-App Notification for Customer
            InAppNotification::send(
                $lockedOrder->user_id,
                'Pembayaran Ditolak',
                "Pembayaran pesanan {$lockedOrder->order_number} ditolak dengan alasan: '{$trimmedReason}'. Silakan unggah ulang bukti transfer yang valid.",
                'error',
                route('customer.orders.show', $lockedOrder->id)
            );
        });
    }

    /**
     * Securely serve payment proof file from private disk with authorization.
     */
    public function serveProof(PaymentProof $proof, User $currentUser): StreamedResponse|BinaryFileResponse
    {
        $order = $proof->payment->order;

        // Authorization check: Admin OR Owner
        if (!$currentUser->isAdmin() && $order->user_id !== $currentUser->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti pembayaran ini.');
        }

        if (!Storage::disk('private')->exists($proof->file_path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan pada penyimpanan privat.');
        }

        return Storage::disk('private')->response($proof->file_path, $proof->original_filename, [
            'Content-Type' => $proof->mime_type,
            'Content-Disposition' => 'inline; filename="' . $proof->original_filename . '"',
        ]);
    }
}
