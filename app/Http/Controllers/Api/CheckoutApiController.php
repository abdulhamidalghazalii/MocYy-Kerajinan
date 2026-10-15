<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutApiController extends Controller
{
    /**
     * Process checkout from the frontend Next.js app.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:25',
            'full_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
            'customer_note' => 'nullable|string|max:500',
            'greeting_card_note' => 'nullable|string|max:500',
            'customer_email' => 'nullable|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $shippingMethod = ShippingMethod::where('is_active', true)->findOrFail($request->shipping_method_id);
            $paymentMethod = PaymentMethod::where('is_active', true)->findOrFail($request->payment_method_id);

            // Find or associate user (customer demo or create guest)
            $email = $request->input('customer_email', 'guest@customer.local');
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $request->recipient_name,
                    'phone' => $request->recipient_phone,
                    'role' => 'customer',
                    'password' => bcrypt(Str::random(16)),
                    'is_active' => true,
                ]
            );
            if (!$user->wasRecentlyCreated && empty($user->phone)) {
                $user->update([
                    'phone' => $request->recipient_phone,
                ]);
            }

            $order = DB::transaction(function () use ($request, $user, $shippingMethod, $paymentMethod) {
                $subtotal = 0.00;
                $orderItemsData = [];

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if (!$product->is_active) {
                        throw new Exception("Produk '{$product->name}' sedang tidak aktif.");
                    }

                    $variant = null;
                    $itemPrice = (float) $product->effective_price;

                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::lockForUpdate()->findOrFail($item['variant_id']);
                        if ($variant->stock < $item['quantity']) {
                            throw new Exception("Stok varian '{$variant->variant_value}' untuk {$product->name} tidak mencukupi (sisa: {$variant->stock}).");
                        }
                        $itemPrice += (float) $variant->additional_price;
                        $variant->decrement('stock', $item['quantity']);
                    } else {
                        if ($product->stock < $item['quantity']) {
                            throw new Exception("Stok untuk {$product->name} tidak mencukupi (sisa: {$product->stock}).");
                        }
                    }

                    $product->decrement('stock', $item['quantity']);

                    $lineTotal = $itemPrice * $item['quantity'];
                    $subtotal += $lineTotal;

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'product_name' => $product->name,
                        'variant_info' => $variant ? "{$variant->variant_name}: {$variant->variant_value}" : null,
                        'sku' => $product->sku,
                        'unit_price' => $itemPrice,
                        'quantity' => $item['quantity'],
                        'subtotal' => $lineTotal,
                        'custom_note' => $request->customer_note,
                        'greeting_card_text' => $request->greeting_card_note,
                    ];
                }

                $shippingCost = (float) $shippingMethod->cost;
                $grandTotal = $subtotal + $shippingCost;

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => $user->id,
                    'shipping_method_id' => $shippingMethod->id,
                    'payment_method_id' => $paymentMethod->id,
                    'recipient_name' => $request->recipient_name,
                    'recipient_phone' => $request->recipient_phone,
                    'shipping_address' => $request->full_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'customer_note' => $request->customer_note,
                    'greeting_card_note' => $request->greeting_card_note,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => 0.00,
                    'grand_total' => $grandTotal,
                    'payment_status' => 'unpaid',
                    'order_status' => 'pending_payment',
                ]);

                foreach ($orderItemsData as $itemData) {
                    $itemData['order_id'] = $order->id;
                    OrderItem::create($itemData);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $grandTotal,
                    'status' => 'unpaid',
                    'transaction_ref' => 'TRX-' . strtoupper(Str::random(10)),
                ]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'type' => 'order',
                    'previous_status' => null,
                    'new_status' => 'pending_payment',
                    'notes' => 'Pesanan berhasil dibuat melalui Online Storefront 3D.',
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data' => [
                    'order_number' => $order->order_number,
                    'recipient_name' => $order->recipient_name,
                    'subtotal' => (float) $order->subtotal,
                    'shipping_cost' => (float) $order->shipping_cost,
                    'grand_total' => (float) $order->grand_total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'payment_method' => [
                        'name' => $paymentMethod->name,
                        'code' => $paymentMethod->code,
                        'account_name' => $paymentMethod->account_name,
                        'account_number' => $paymentMethod->account_number,
                        'qr_image_url' => $paymentMethod->qr_image_url,
                        'instructions' => $paymentMethod->instructions,
                        'expiry_minutes' => $paymentMethod->expiry_minutes,
                    ],
                    'shipping_method' => [
                        'name' => $shippingMethod->name,
                        'estimated_time' => $shippingMethod->estimated_time,
                    ],
                ],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Track order by order_number.
     */
    public function track(string $orderNumber): JsonResponse
    {
        $order = Order::with(['items', 'paymentMethod', 'shippingMethod', 'histories'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'created_at' => $order->created_at->format('d M Y, H:i'),
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'recipient_name' => $order->recipient_name,
                'shipping_address' => $order->shipping_address,
                'city' => $order->city,
                'tracking_number' => $order->tracking_number,
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'grand_total' => (float) $order->grand_total,
                'payment_method' => $order->paymentMethod ? [
                    'name' => $order->paymentMethod->name,
                    'code' => $order->paymentMethod->code,
                    'account_name' => $order->paymentMethod->account_name,
                    'account_number' => $order->paymentMethod->account_number,
                    'qr_image_url' => $order->paymentMethod->qr_image_url,
                ] : null,
                'items' => $order->items->map(fn($item) => [
                    'product_name' => $item->product_name,
                    'variant_info' => $item->variant_info,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]),
                'history' => $order->histories->map(fn($h) => [
                    'status' => $h->status,
                    'notes' => $h->notes,
                    'created_at' => $h->created_at->format('d M Y, H:i'),
                ]),
            ],
        ]);
    }
}
