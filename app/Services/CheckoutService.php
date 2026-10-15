<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Process checkout with DB transaction and pessimistic locking.
     */
    public function processCheckout(
        User $user,
        Cart $cart,
        array $shippingData,
        int $shippingMethodId,
        int $paymentMethodId,
        ?string $customerNote = null,
        ?string $greetingCardNote = null
    ): Order {
        if ($cart->items->isEmpty()) {
            throw new Exception('Keranjang belanja Anda masih kosong.');
        }

        $shippingMethod = ShippingMethod::where('is_active', true)->findOrFail($shippingMethodId);
        $paymentMethod = PaymentMethod::where('is_active', true)->findOrFail($paymentMethodId);

        return DB::transaction(function () use (
            $user,
            $cart,
            $shippingData,
            $shippingMethod,
            $paymentMethod,
            $customerNote,
            $greetingCardNote
        ) {
            $subtotal = 0.00;
            $orderItemsData = [];

            // 1. Pessimistic Locking & Server-side Price Calculation
            foreach ($cart->items as $cartItem) {
                // Lock product row for update to prevent overselling race conditions
                $product = Product::lockForUpdate()->find($cartItem->product_id);
                if (!$product || !$product->is_active) {
                    throw new Exception("Produk '{$cartItem->product?->name}' tidak tersedia atau sudah nonaktif.");
                }

                $variant = null;
                $availableStock = $product->stock;
                $unitPrice = $product->effective_price;

                if ($cartItem->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($cartItem->product_variant_id);
                    if (!$variant) {
                        throw new Exception("Variasi produk '{$product->name}' tidak ditemukan.");
                    }
                    if ($variant->stock > 0) {
                        $availableStock = min($availableStock, $variant->stock);
                    }
                    $unitPrice += (float) $variant->additional_price;
                }

                if ($availableStock < $cartItem->quantity) {
                    throw new Exception("Maaf, stok untuk '{$product->name}' tidak mencukupi (tersisa {$availableStock}).");
                }

                // Decrement stock safely
                $product->stock -= $cartItem->quantity;
                $product->save();

                if ($variant && $variant->stock > 0) {
                    $variant->stock -= $cartItem->quantity;
                    $variant->save();
                }

                $itemSubtotal = $unitPrice * $cartItem->quantity;
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'variant_info' => $variant ? "{$variant->variant_name}: {$variant->variant_value}" : null,
                    'sku' => $variant && $variant->sku ? $variant->sku : $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $itemSubtotal,
                    'custom_note' => $cartItem->custom_note,
                    'greeting_card_text' => $cartItem->greeting_card_text,
                ];
            }

            $shippingCost = (float) $shippingMethod->cost;
            $discountAmount = 0.00;
            $grandTotal = $subtotal + $shippingCost - $discountAmount;

            // 2. Create Order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'shipping_method_id' => $shippingMethod->id,
                'payment_method_id' => $paymentMethod->id,
                'recipient_name' => $shippingData['recipient_name'],
                'recipient_phone' => $shippingData['recipient_phone'],
                'shipping_address' => $shippingData['full_address'],
                'city' => $shippingData['city'],
                'postal_code' => $shippingData['postal_code'] ?? null,
                'customer_note' => $customerNote,
                'greeting_card_note' => $greetingCardNote,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'payment_status' => 'unpaid',
                'order_status' => 'pending_payment',
            ]);

            // 3. Save Snapshots to OrderItems
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // 4. Create Initial Payment Record
            Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $grandTotal,
                'status' => 'unpaid',
            ]);

            // 5. Create Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'type' => 'order',
                'previous_status' => null,
                'new_status' => 'pending_payment',
                'notes' => 'Pesanan berhasil dibuat. Menunggu pembayaran dari customer.',
            ]);

            // 6. Clear Customer's Cart
            $cart->items()->delete();

            // 7. Send In-App Notification
            InAppNotification::send(
                $user->id,
                'Pesanan Berhasil Dibuat!',
                "Pesanan {$order->order_number} berhasil dibuat. Silakan selesaikan pembayaran sebesar Rp " . number_format($grandTotal, 0, ',', '.') . " melalui metode {$paymentMethod->name}.",
                'info',
                route('customer.orders.show', $order->id)
            );

            return $order;
        });
    }
}
