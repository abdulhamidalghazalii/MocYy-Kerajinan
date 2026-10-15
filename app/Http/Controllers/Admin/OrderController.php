<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'paymentMethod', 'shippingMethod']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                  ->orWhere('recipient_name', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%");
                  });
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = Order::with([
            'user',
            'paymentMethod',
            'shippingMethod',
            'items.product',
            'payments.proofs',
            'statusHistories.actor',
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => [
                'required',
                'in:pending_payment,processing,in_production,ready_to_ship,shipped,completed,cancelled',
            ],
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->order_status;
        $previousStatus = $order->order_status;

        // Restriction: Unpaid orders cannot be moved to production, shipping, or completed
        if (in_array($newStatus, ['processing', 'in_production', 'ready_to_ship', 'shipped', 'completed']) && $order->payment_status !== 'paid') {
            return back()->with('error', 'Pesanan belum lunas / pembayaran belum diverifikasi. Status tidak dapat dimajukan sebelum pembayaran berstatus LUNAS (PAID).');
        }

        DB::transaction(function () use ($order, $newStatus, $previousStatus, $request) {
            $admin = Auth::user();

            $updateData = ['order_status' => $newStatus];
            if ($request->filled('tracking_number')) {
                $updateData['tracking_number'] = $request->tracking_number;
            }
            if ($newStatus === 'cancelled') {
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = $request->notes ?? 'Dibatalkan oleh Admin';
            }

            $order->update($updateData);

            // Record History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $admin->id,
                'type' => 'order',
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'notes' => $request->notes ?: "Status pesanan diubah oleh {$admin->name}.",
            ]);

            // Activity Log
            AdminActivityLog::record(
                $admin->id,
                'order_status_updated',
                Order::class,
                $order->id,
                [
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'tracking_number' => $request->tracking_number,
                ]
            );

            // Notification for Customer
            $statusLabels = [
                'in_production' => 'Bucket pesanan Anda sedang mulai dirangkai oleh florist kami.',
                'ready_to_ship' => 'Rangkaian bucket pesanan Anda telah selesai dan siap dikirim / diambil.',
                'shipped' => "Pesanan Anda telah dikirim! Nomor resi / kurir: " . ($request->tracking_number ?: '-'),
                'completed' => 'Pesanan Anda telah selesai. Terima kasih telah mempercayakan hadiah Anda kepada kami! ❤️',
                'cancelled' => 'Pesanan Anda telah dibatalkan oleh pihak toko.',
            ];

            if (isset($statusLabels[$newStatus])) {
                InAppNotification::send(
                    $order->user_id,
                    'Update Status Pesanan: ' . $order->order_status_label,
                    $statusLabels[$newStatus],
                    $newStatus === 'cancelled' ? 'error' : 'info',
                    route('customer.orders.show', $order->id)
                );
            }
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
