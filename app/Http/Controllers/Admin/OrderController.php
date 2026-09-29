<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'user'])->latest();

        // 1. Keyword search
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                  ->orWhere('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('customer_phone', 'like', "%{$keyword}%");
            });
        }

        // 2. Filter status
        if ($status = $request->input('status')) {
            $query->where('order_status', $status);
        }

        // 3. Filter payment status
        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        // 4. Filter date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(15)->withQueryString();

        // KPI Counts
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $manufacturingOrders = Order::whereIn('order_status', ['confirmed', 'manufacturing', 'shipping'])->count();
        $completedOrders = Order::where('order_status', 'completed')->count();
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();
        $paidRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $completedRevenue = Order::where('order_status', 'completed')->sum('total_amount');

        return view('admin.orders.index', compact(
            'orders',
            'totalOrders',
            'pendingOrders',
            'manufacturingOrders',
            'completedOrders',
            'cancelledOrders',
            'paidRevenue',
            'completedRevenue'
        ));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,confirmed,manufacturing,shipping,installed,completed,cancelled',
        ]);

        $order = Order::with('items')->findOrFail($id);
        $oldStatus = $order->order_status;
        $order->order_status = $request->order_status;

        // Nếu chuyển sang trạng thái đã hủy -> Tự động hoàn lại tồn kho cho các sản phẩm
        if ($request->order_status === 'cancelled') {
            $order->restoreStock();
        }

        $order->save();

        \App\Models\AuditLog::record(
            'status',
            'orders',
            "Cập nhật tiến độ đơn hàng #{$order->order_code} từ '{$oldStatus}' sang '{$order->order_status}'",
            $order->order_code,
            ['order_status' => $oldStatus],
            ['order_status' => $order->order_status]
        );

        return redirect()->back()->with('success', "Đã cập nhật trạng thái đơn hàng {$order->order_code} thành công.");
    }

    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,partially_paid,paid',
        ]);

        $order = Order::findOrFail($id);
        $oldPayment = $order->payment_status;
        $order->payment_status = $request->payment_status;
        $order->save();

        \App\Models\AuditLog::record(
            'update',
            'orders',
            "Cập nhật thanh toán đơn hàng #{$order->order_code} thành '{$order->payment_status}'",
            $order->order_code,
            ['payment_status' => $oldPayment],
            ['payment_status' => $order->payment_status]
        );

        return redirect()->back()->with('success', "Đã cập nhật trạng thái thanh toán đơn {$order->order_code}.");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'bulk_status' => 'required|in:pending,confirmed,manufacturing,shipping,completed,cancelled',
        ]);

        if ($request->bulk_status === 'cancelled') {
            $orders = Order::with('items')->whereIn('id', $request->order_ids)->get();
            foreach ($orders as $order) {
                $order->restoreStock();
                $order->update(['order_status' => 'cancelled']);
            }
        } else {
            Order::whereIn('id', $request->order_ids)
                ->update(['order_status' => $request->bulk_status]);
        }

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái các đơn hàng đã chọn.');
    }
}
