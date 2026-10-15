<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)
            ->with(['items.product', 'paymentMethod', 'shippingMethod']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = Order::with([
            'items.product',
            'paymentMethod',
            'shippingMethod',
            'payments.proofs',
            'statusHistories.actor',
        ])->findOrFail($id);

        if (!Gate::allows('view-order', $order)) {
            abort(403, 'Anda tidak berhak melihat pesanan ini.');
        }

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if (!Gate::allows('cancel-order', $order)) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan (hanya pesanan belum dibayar yang dapat dibatalkan).');
        }

        $reason = $request->input('reason', 'Dibatalkan oleh pelanggan.');

        DB::transaction(function () use ($order, $reason) {
            $previousStatus = $order->order_status;

            // Restock items
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                }
            }

            $order->update([
                'order_status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'type' => 'order',
                'previous_status' => $previousStatus,
                'new_status' => 'cancelled',
                'notes' => 'Pesanan dibatalkan oleh pembeli. Alasan: ' . $reason,
            ]);
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan dan stok telah dikembalikan.');
    }
}
