<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items', 'paymentMethod'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_payment' => Order::where('user_id', $user->id)->where('payment_status', 'unpaid')->count(),
            'pending_review' => Order::where('user_id', $user->id)->where('payment_status', 'pending_review')->count(),
            'processing' => Order::where('user_id', $user->id)->whereIn('order_status', ['processing', 'in_production', 'ready_to_ship'])->count(),
            'completed' => Order::where('user_id', $user->id)->where('order_status', 'completed')->count(),
        ];

        $notifications = InAppNotification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('user', 'recentOrders', 'stats', 'notifications'));
    }
}
