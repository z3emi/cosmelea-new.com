<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // طلبات حسب الحالة
        $totalOrders       = Order::count();
        $pendingOrders     = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $returnedOrders    = Order::where('status', 'returned')->count();

        // العملاء النشطين
        $activeCustomers = User::whereHas('orders')->count();

        // الطلبات التي تمت اليوم
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

        // إجمالي المبيعات
        $totalSales = Order::where('status', 'delivered')
            ->sum(DB::raw('total_amount - shipping_cost'));

        // آخر الطلبات
        $latestOrders = Order::with('customer')->latest()->take(5)->get();

        // المنتجات الأكثر مبيعًا
        $topProducts = Product::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // العملاء الأكثر نشاطًا
        $topCustomers = User::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // المنتجات الأكثر ربحية
        $topProfitableProducts = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity * price) as total_revenue, SUM(cost) as total_cost')
            ->groupBy('product_id')
            ->get()
            ->map(function ($item) {
                $item->profit = $item->total_revenue - $item->total_cost;
                return $item;
            })
            ->sortByDesc('profit')
            ->take(5);

        // --- الرسم البياني - عدد الطلبات خلال آخر 30 يوم حسب الحالة ---
        $chartLabels = [];
        $chartTotalOrdersData = [];
        $chartPendingOrdersData = [];
        $chartCompletedOrdersData = [];
        $chartReturnedOrdersData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartLabels[] = $date;

            // Fetch counts for each date and status
            $chartTotalOrdersData[]     = Order::whereDate('created_at', $date)->count();
            $chartPendingOrdersData[]   = Order::whereDate('created_at', $date)->where('status', 'pending')->count();
            $chartCompletedOrdersData[] = Order::whereDate('created_at', $date)->where('status', 'delivered')->count();
            $chartReturnedOrdersData[]  = Order::whereDate('created_at', $date)->where('status', 'returned')->count();
        }

        // تمرير البيانات إلى الـ view
        return view('admin.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'returnedOrders',
            'activeCustomers',
            'todayOrders',
            'totalSales',
            'latestOrders',
            'topProducts',
            'topCustomers',
            'topProfitableProducts',
            'chartLabels',
            'chartTotalOrdersData',      // New
            'chartPendingOrdersData',    // New
            'chartCompletedOrdersData',  // New
            'chartReturnedOrdersData'    // New
        ));
    }
}