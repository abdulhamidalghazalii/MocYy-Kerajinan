<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\InAppNotification;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'customer',
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Merge guest cart
        $this->cartService->mergeGuestCart($user->id);

        InAppNotification::send(
            $user->id,
            'Selamat Datang di MocYy!',
            'Akun Anda berhasil dibuat. Nikmati kemudahan berbelanja berbagai koleksi buket bunga dan hadiah eksklusif di MocYy.',
            'success',
            route('customer.dashboard')
        );

        return redirect()->route('customer.dashboard')
            ->with('success', 'Pendaftaran akun berhasil! Selamat datang, ' . $user->name . '.');
    }
}
