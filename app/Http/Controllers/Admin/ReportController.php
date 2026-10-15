<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $status = $request->get('payment_status', 'paid');

        $query = Order::with(['user', 'paymentMethod', 'shippingMethod'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        $orders = (clone $query)->latest()->paginate(20)->withQueryString();

        // Financial Summary: Only PAID transactions represent real revenue
        $totalPaidRevenue = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $totalPaidOrdersCount = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->count();

        $avgOrderValue = $totalPaidOrdersCount > 0 ? ($totalPaidRevenue / $totalPaidOrdersCount) : 0;

        return view('admin.reports.index', compact(
            'orders',
            'dateFrom',
            'dateTo',
            'status',
            'totalPaidRevenue',
            'totalPaidOrdersCount',
            'avgOrderValue'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $status = $request->get('payment_status', 'paid');

        $query = Order::with(['user', 'paymentMethod', 'shippingMethod', 'items'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        $orders = $query->latest()->get();

        $filename = "laporan_penjualan_{$dateFrom}_sampai_{$dateTo}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Indonesian Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No. Pesanan',
                'Tanggal Pesanan',
                'Nama Pembeli',
                'Email',
                'Telepon',
                'Penerima',
                'Kota',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Status Pesanan',
                'Subtotal (IDR)',
                'Ongkir (IDR)',
                'Total Akhir (IDR)',
                'Waktu Pembayaran Diverifikasi',
            ], ';');

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user ? $order->user->name : '-',
                    $order->user ? $order->user->email : '-',
                    $order->user ? $order->user->phone : '-',
                    $order->recipient_name,
                    $order->city,
                    $order->paymentMethod ? $order->paymentMethod->name : '-',
                    $order->payment_status_label,
                    $order->order_status_label,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->grand_total,
                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i') : '-',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }
}
