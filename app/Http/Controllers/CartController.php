<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): View
    {
        $cart = $this->cartService->getCart();
        return view('frontend.cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:50',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'custom_note' => 'nullable|string|max:500',
            'greeting_card_text' => 'nullable|string|max:500',
        ]);

        $result = $this->cartService->addItem(
            (int) $request->product_id,
            (int) $request->quantity,
            $request->filled('product_variant_id') ? (int) $request->product_variant_id : null,
            $request->custom_note,
            $request->greeting_card_text
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        if ($request->has('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:50',
        ]);

        $result = $this->cartService->updateItemQuantity($id, (int) $request->quantity);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->cartService->removeItem($id);
        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();
        return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }
}
