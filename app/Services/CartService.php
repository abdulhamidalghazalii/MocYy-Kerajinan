<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create current cart (for authenticated customer or guest session).
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
        }

        return $cart->load(['items.product', 'items.variant']);
    }

    /**
     * Merge guest cart into user cart upon login.
     */
    public function mergeGuestCart(int $userId): void
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();

        if (!$guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            $existing = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                $item->cart_id = $userCart->id;
                $item->save();
            }
        }

        $guestCart->delete();
    }

    /**
     * Add product to cart with server-side validation.
     */
    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null, ?string $customNote = null, ?string $greetingCardText = null): array
    {
        $product = Product::active()->find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan atau sedang tidak aktif.'];
        }

        // Validate stock
        $availableStock = $product->stock;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)->find($variantId);
            if (!$variant) {
                return ['success' => false, 'message' => 'Variasi produk tidak valid.'];
            }
            if ($variant->stock > 0) {
                $availableStock = min($availableStock, $variant->stock);
            }
        }

        if ($availableStock < $quantity) {
            return ['success' => false, 'message' => "Stok tidak mencukupi. Tersedia hanya {$availableStock} unit."];
        }

        $cart = $this->getCart();

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($newQuantity > $availableStock) {
                return ['success' => false, 'message' => "Jumlah di keranjang melebihi sisa stok ({$availableStock})."];
            }
            $cartItem->quantity = $newQuantity;
            if ($customNote !== null) $cartItem->custom_note = $customNote;
            if ($greetingCardText !== null) $cartItem->greeting_card_text = $greetingCardText;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'custom_note' => $customNote,
                'greeting_card_text' => $greetingCardText,
            ]);
        }

        return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang!'];
    }

    /**
     * Update item quantity in cart.
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): array
    {
        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->find($cartItemId);

        if (!$item) {
            return ['success' => false, 'message' => 'Item keranjang tidak ditemukan.'];
        }

        if ($quantity <= 0) {
            $item->delete();
            return ['success' => true, 'message' => 'Item dihapus dari keranjang.'];
        }

        $product = $item->product;
        $maxStock = $item->variant && $item->variant->stock > 0 ? min($product->stock, $item->variant->stock) : $product->stock;

        if ($quantity > $maxStock) {
            return ['success' => false, 'message' => "Stok maksimal hanya {$maxStock}."];
        }

        $item->quantity = $quantity;
        $item->save();

        return ['success' => true, 'message' => 'Jumlah produk diperbarui.'];
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $cartItemId): bool
    {
        $cart = $this->getCart();
        return (bool) CartItem::where('cart_id', $cart->id)->where('id', $cartItemId)->delete();
    }

    /**
     * Clear all items in cart.
     */
    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }
}
