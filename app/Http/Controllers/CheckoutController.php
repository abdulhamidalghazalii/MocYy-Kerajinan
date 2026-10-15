<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function index(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Silakan masuk atau buat akun baru terlebih dahulu untuk menyelesaikan pesanan Anda.');
        }

        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih bucket atau gift terlebih dahulu.');
        }

        $user = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();
        $defaultAddress = $addresses->first();
        $shippingMethods = ShippingMethod::where('is_active', true)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();

        return view('frontend.checkout.index', compact(
            'cart',
            'user',
            'addresses',
            'defaultAddress',
            'shippingMethods',
            'paymentMethods'
        ));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart();

        $shippingData = [
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ];

        // Save new address if requested
        if ($request->boolean('save_address')) {
            Address::create([
                'user_id' => $user->id,
                'label' => $request->input('address_label', 'Alamat Pengiriman'),
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'full_address' => $request->full_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'is_default' => $user->addresses()->count() === 0,
            ]);
        }

        try {
            $order = $this->checkoutService->processCheckout(
                $user,
                $cart,
                $shippingData,
                (int) $request->shipping_method_id,
                (int) $request->payment_method_id,
                $request->customer_note,
                $request->greeting_card_note
            );

            return redirect()->route('customer.orders.payment', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran dan unggah bukti transfer.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
