<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return view('customer.profile.edit', compact('user', 'addresses'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:25',
            'full_address' => 'required|string|max:1000',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => $user->id,
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
            'is_default' => $request->boolean('is_default') || $user->addresses()->count() === 0,
        ]);

        return back()->with('success', 'Alamat pengiriman baru berhasil disimpan.');
    }

    public function destroyAddress(int $id): RedirectResponse
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Alamat pengiriman berhasil dihapus.');
    }
}
