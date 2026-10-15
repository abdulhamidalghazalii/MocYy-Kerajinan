<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customer accounts.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('orders');

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $customers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('is_active', true)->count();
        $inactiveCustomers = User::where('role', 'customer')->where('is_active', false)->count();

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'inactiveCustomers'
        ));
    }

    /**
     * Show the form for creating a new customer account.
     */
    public function create(): View
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:25',
            'password' => 'required|string|min:6',
            'is_active' => 'boolean',
        ]);

        $customer = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'customer',
            'is_active' => $request->boolean('is_active', true),
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        AdminActivityLog::log(
            auth()->id(),
            'create_customer',
            "Membuat akun customer baru: {$customer->name} ({$customer->email})",
            $customer
        );

        return redirect()->route('admin.customers.index')
            ->with('success', "Akun customer '{$customer->name}' ({$customer->email}) berhasil dibuat.");
    }

    /**
     * Display the specified customer detail.
     */
    public function show(User|int|string $customer): View
    {
        $customer = $customer instanceof User ? $customer : User::where('role', 'customer')->findOrFail($customer);
        $customer->load(['addresses', 'orders' => function ($q) {
            $q->orderByDesc('created_at');
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the customer account.
     */
    public function edit(User|int|string $customer): View
    {
        $customer = $customer instanceof User ? $customer : User::where('role', 'customer')->findOrFail($customer);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the customer account details & email.
     */
    public function update(Request $request, User|int|string $customer): RedirectResponse
    {
        $customer = $customer instanceof User ? $customer : User::where('role', 'customer')->findOrFail($customer);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($customer->id),
            ],
            'phone' => 'nullable|string|max:25',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $oldEmail = $customer->email;
        $customer->name = $validated['name'];
        $customer->email = strtolower($validated['email']);
        $customer->phone = $validated['phone'] ?? null;
        $customer->is_active = $request->boolean('is_active', true);

        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        AdminActivityLog::log(
            auth()->id(),
            'update_customer',
            "Memperbarui akun customer {$customer->name} (Email lama: {$oldEmail} -> Email baru: {$customer->email})",
            $customer
        );

        return redirect()->route('admin.customers.index')
            ->with('success', "Akun customer '{$customer->name}' berhasil diperbarui.");
    }

    /**
     * Toggle customer active status.
     */
    public function toggleStatus(User|int|string $customer): RedirectResponse
    {
        $customer = $customer instanceof User ? $customer : User::where('role', 'customer')->findOrFail($customer);
        $customer->is_active = !$customer->is_active;
        $customer->save();

        $statusText = $customer->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AdminActivityLog::log(
            auth()->id(),
            'toggle_customer_status',
            "Mengubah status akun customer {$customer->email} menjadi {$statusText}",
            $customer
        );

        return back()->with('success', "Status akun customer {$customer->email} berhasil {$statusText}.");
    }

    /**
     * Remove the customer account from storage.
     */
    public function destroy(User|int|string $customer): RedirectResponse
    {
        $customer = $customer instanceof User ? $customer : User::where('role', 'customer')->findOrFail($customer);

        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return back()->with('error', "Customer '{$customer->name}' memiliki riwayat pesanan aktif dan tidak dapat dihapus permanen. Anda dapat menonaktifkan akun tersebut.");
        }

        $name = $customer->name;
        $email = $customer->email;
        $customer->delete();

        AdminActivityLog::log(
            auth()->id(),
            'delete_customer',
            "Menghapus akun customer: {$name} ({$email})"
        );

        return redirect()->route('admin.customers.index')
            ->with('success', "Akun customer '{$name}' ({$email}) berhasil dihapus.");
    }
}
