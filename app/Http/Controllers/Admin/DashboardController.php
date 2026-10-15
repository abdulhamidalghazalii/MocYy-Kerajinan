<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // 1. Total Omzet ONLY from verified/paid payments
        $totalRevenue = Order::where('payment_status', 'paid')->sum('grand_total');

        // Revenue today (paid only)
        $revenueToday = Order::where('payment_status', 'paid')
            ->whereDate('paid_at', $today)
            ->sum('grand_total');

        // Order Counts
        $ordersTodayCount = Order::whereDate('created_at', $today)->count();
        $pendingPaymentCount = Order::where('payment_status', 'unpaid')->where('order_status', '!=', 'cancelled')->count();
        $pendingReviewCount = Order::where('payment_status', 'pending_review')->count();
        $rejectedPaymentCount = Order::where('payment_status', 'rejected')->count();

        // Fulfillment status counts (Paid & active orders)
        $processingCount = Order::where('order_status', 'processing')->count();
        $inProductionCount = Order::where('order_status', 'in_production')->count();
        $readyToShipCount = Order::where('order_status', 'ready_to_ship')->count();
        $shippedCount = Order::where('order_status', 'shipped')->count();

        // Low stock products (less than or equal to 5)
        $lowStockProducts = Product::active()->where('stock', '<=', 5)->take(5)->get();

        // Recent Orders
        $recentOrders = Order::with(['user', 'paymentMethod'])
            ->latest()
            ->take(8)
            ->get();

        // Sales Chart: last 7 days of verified revenue
        $chartDates = [];
        $chartRevenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dailyRevenue = Order::where('payment_status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('grand_total');

            $chartDates[] = $date->translatedFormat('d M');
            $chartRevenues[] = (float) $dailyRevenue;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'revenueToday',
            'ordersTodayCount',
            'pendingPaymentCount',
            'pendingReviewCount',
            'rejectedPaymentCount',
            'processingCount',
            'inProductionCount',
            'readyToShipCount',
            'shippedCount',
            'lowStockProducts',
            'recentOrders',
            'chartDates',
            'chartRevenues'
        ));
    }
}
