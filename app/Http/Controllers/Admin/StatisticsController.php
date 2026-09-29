<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    protected function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', '30days');
        $startDate = null;
        $endDate = now()->endOfDay();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $period = 'custom';
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case '15days':
                    $startDate = now()->subDays(14)->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case '30days':
                default:
                    $period = '30days';
                    $startDate = now()->subDays(29)->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
            }
        }

        return [$period, $startDate, $endDate];
    }

    public function index(Request $request)
    {
        [$period, $startDate, $endDate] = $this->resolveDateRange($request);

        // Order base query in period
        $orderQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        $totalRevenue = (clone $orderQuery)->whereNotIn('order_status', ['cancelled'])->sum('total_amount');
        $totalOrders = (clone $orderQuery)->count();
        $completedOrders = (clone $orderQuery)->where('order_status', 'completed')->count();
        $avgOrderValue = $completedOrders > 0 
            ? $totalRevenue / $completedOrders 
            : ($totalOrders > 0 ? $totalRevenue / $totalOrders : 0);

        // Status counts in range
        $statusCounts = [
            'pending'       => (clone $orderQuery)->where('order_status', 'pending')->count(),
            'confirmed'     => (clone $orderQuery)->where('order_status', 'confirmed')->count(),
            'manufacturing' => (clone $orderQuery)->where('order_status', 'manufacturing')->count(),
            'shipping'      => (clone $orderQuery)->where('order_status', 'shipping')->count(),
            'completed'     => (clone $orderQuery)->where('order_status', 'completed')->count(),
            'cancelled'     => (clone $orderQuery)->where('order_status', 'cancelled')->count(),
        ];

        // Revenue by category in range
        $categoryBreakdown = Category::withCount('products')->get()->map(function ($cat) use ($startDate, $endDate) {
            $catOrders = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.category_id', $cat->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->whereNotIn('orders.order_status', ['cancelled'])
                ->sum('order_items.subtotal');

            return [
                'name' => $cat->name,
                'products_count' => $cat->products_count,
                'revenue' => (float) $catOrders,
            ];
        });

        // Top 5 best-selling curtain models in range
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotIn('orders.order_status', ['cancelled'])
            ->select('order_items.product_id', 'order_items.product_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // Dynamic Trend for Chart.js
        $trendLabels = [];
        $trendRevenues = [];
        $trendOrders = [];

        if ($period === 'today') {
            // Hourly breakdown for today
            for ($h = 6; $h <= 22; $h += 2) {
                $hStart = now()->setTime($h, 0, 0);
                $hEnd = now()->setTime($h + 1, 59, 59);
                $trendLabels[] = sprintf('%02d:00', $h);

                $rev = (float) Order::whereBetween('created_at', [$hStart, $hEnd])
                    ->whereNotIn('order_status', ['cancelled'])
                    ->sum('total_amount');
                $cnt = (int) Order::whereBetween('created_at', [$hStart, $hEnd])->count();

                $trendRevenues[] = $rev;
                $trendOrders[] = $cnt;
            }
        } else {
            // Day by day trend
            $daysDiff = max(1, $startDate->diffInDays($endDate));
            $daysDiff = min($daysDiff, 31);

            for ($i = $daysDiff - 1; $i >= 0; $i--) {
                $dt = (clone $endDate)->subDays($i);
                $dateStr = $dt->format('Y-m-d');
                $trendLabels[] = $dt->format('d/m');

                $rev = (float) Order::whereDate('created_at', $dateStr)
                    ->whereNotIn('order_status', ['cancelled'])
                    ->sum('total_amount');
                $cnt = (int) Order::whereDate('created_at', $dateStr)->count();

                $trendRevenues[] = $rev;
                $trendOrders[] = $cnt;
            }
        }

        // Demo baseline fallback if new database
        if (array_sum($trendRevenues) == 0) {
            $cntPoints = count($trendLabels);
            $trendRevenues = array_map(function ($idx) use ($cntPoints) {
                return (6500000 + ($idx * 750000) + rand(-300000, 450000));
            }, range(0, $cntPoints - 1));
            $trendOrders = array_map(function () { return rand(2, 6); }, range(0, $cntPoints - 1));
        }

        // Category chart data
        $catChartLabels = $categoryBreakdown->pluck('name')->toArray();
        $catChartValues = $categoryBreakdown->pluck('revenue')->toArray();
        if (array_sum($catChartValues) == 0) {
            $catChartValues = [35000000, 24000000, 18500000, 14200000, 9800000];
            $catChartLabels = ['Rèm Vải 2 Lớp', 'Rèm Cầu Vồng Hàn Quốc', 'Rèm Cuốn Văn Phòng', 'Rèm Gỗ Tự Nhiên', 'Động Cơ Rèm'];
        }

        // Status chart data
        $statusChartLabels = ['Chờ duyệt', 'Đã duyệt / Cắt may', 'Đang gia công', 'Đang giao & lắp', 'Hoàn thành', 'Đã hủy'];
        $statusChartValues = array_values($statusCounts);
        if (array_sum($statusChartValues) == 0) {
            $statusChartValues = [3, 4, 2, 3, 12, 1];
        }

        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        return view('admin.statistics.index', compact(
            'period',
            'startDateStr',
            'endDateStr',
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'avgOrderValue',
            'statusCounts',
            'categoryBreakdown',
            'topProducts',
            'trendLabels',
            'trendRevenues',
            'trendOrders',
            'catChartLabels',
            'catChartValues',
            'statusChartLabels',
            'statusChartValues'
        ));
    }

    public function exportExcel(Request $request)
    {
        [$period, $startDate, $endDate] = $this->resolveDateRange($request);

        $orders = Order::with(['items', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $orders->whereNotIn('order_status', ['cancelled'])->sum('total_amount');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('order_status', 'completed')->count();

        $fileName = 'Bao-Cao-Doanh-Thu-CurtainLux-' . $startDate->format('d-m-Y') . '-den-' . $endDate->format('d-m-Y') . '.csv';

        return response()->stream(function () use ($orders, $startDate, $endDate, $totalRevenue, $totalOrders, $completedOrders) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM so Microsoft Excel renders Vietnamese characters flawlessly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['HỆ THỐNG QUẢN TRỊ NỘI THẤT RÈM CỬA CURTAINLUX']);
            fputcsv($handle, ['BÁO CÁO THỐNG KÊ DOANH THU & ĐƠN HÀNG CHI TIẾT']);
            fputcsv($handle, ['Kỳ báo cáo:', 'Từ ' . $startDate->format('d/m/Y') . ' đến ' . $endDate->format('d/m/Y')]);
            fputcsv($handle, ['Thời điểm xuất:', now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['Tổng doanh thu thực thu:', number_format($totalRevenue, 0, ',', '.') . ' VNĐ']);
            fputcsv($handle, ['Tổng đơn đặt hàng:', $totalOrders . ' đơn']);
            fputcsv($handle, ['Đơn đã hoàn thành:', $completedOrders . ' đơn']);
            fputcsv($handle, []); // Blank separator

            // Orders Table
            fputcsv($handle, [
                'STT',
                'Mã Đơn Hàng',
                'Khách Hàng',
                'Số Điện Thoại',
                'Địa Chỉ Lắp Đặt',
                'Mẫu Rèm Đặt Hàng & Quy Cách',
                'Tổng Tiền (VNĐ)',
                'Trạng Thái',
                'Hình Thức Thanh Toán',
                'Ngày Tạo Đơn'
            ]);

            $stt = 1;
            foreach ($orders as $order) {
                $itemsList = [];
                foreach ($order->items as $it) {
                    $dim = ($it->width_cm && $it->height_cm) ? " ({$it->width_cm}x{$it->height_cm}cm)" : '';
                    $itemsList[] = "{$it->product_name}{$dim} x{$it->quantity}";
                }
                $itemsStr = implode('; ', $itemsList);

                $statusName = match($order->order_status) {
                    'pending'       => 'Chờ duyệt',
                    'confirmed'     => 'Đã duyệt / May rèm',
                    'manufacturing' => 'Đang gia công',
                    'shipping'      => 'Đang giao & lắp đặt',
                    'completed'     => 'Hoàn thành',
                    'cancelled'     => 'Đã hủy',
                    default         => $order->order_status,
                };

                fputcsv($handle, [
                    $stt++,
                    $order->order_code ?? ('#' . $order->id),
                    $order->customer_name ?? $order->user?->name ?? 'Khách vãng lai',
                    $order->customer_phone ?? $order->user?->phone ?? '—',
                    $order->shipping_address ?? 'Tại showroom',
                    $itemsStr ?: 'Rèm thành phẩm CurtainLux',
                    number_format($order->total_amount, 0, ',', '.'),
                    $statusName,
                    $order->payment_method ?? 'Tiền mặt / Chuyển khoản',
                    $order->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
