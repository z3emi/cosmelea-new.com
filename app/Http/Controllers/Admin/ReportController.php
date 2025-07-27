<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $this->middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-reports');
    }

    /**
     * Display the main reports dashboard.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Display the inventory reports page.
     * **هذه هي الدالة المفقودة**
     */
    public function inventory(Request $request)
    {
        // جلب كل المنتجات مع حساب الكمية المتبقية
        $products = Product::with('purchaseItems')->get()->map(function ($product) {
            $product->stock_quantity = $product->purchaseItems->sum('quantity_remaining');
            return $product;
        });

        // 1. المنتجات التي على وشك النفاد (أقل من 10 قطع)
        $lowStockProducts = $products->filter(function ($product) {
            return $product->stock_quantity > 0 && $product->stock_quantity < 10;
        })->sortBy('stock_quantity');

        // 2. المنتجات التي نفدت من المخزون
        $outOfStockProducts = $products->filter(function ($product) {
            return $product->stock_quantity <= 0;
        });

        // 3. المنتجات الأكثر مبيعًا خلال آخر 30 يوم
        $topSellingProducts = Product::whereHas('orderItems', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })
        ->withCount(['orderItems' => function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }])
        ->orderBy('order_items_count', 'desc')
        ->take(10)
        ->get();

        return view('admin.reports.inventory', compact(
            'lowStockProducts',
            'outOfStockProducts',
            'topSellingProducts'
        ));
    }

    /**
     * Display the customer reports page.
     */
    public function customers(Request $request)
    {
        // 1. Top customers by spending (delivered orders only)
        $topSpenders = Customer::whereHas('orders', function ($query) {
            $query->where('status', 'delivered');
        })
        ->withSum(['orders' => function ($query) {
            $query->where('status', 'delivered');
        }], 'total_amount')
        ->orderByDesc('orders_sum_total_amount')
        ->take(10)
        ->get();

        // 2. Top customers by number of orders (delivered orders only)
        $mostFrequentBuyers = Customer::whereHas('orders', function ($query) {
            $query->where('status', 'delivered');
        })
        ->withCount(['orders' => function ($query) {
            $query->where('status', 'delivered');
        }])
        ->orderByDesc('orders_count')
        ->take(10)
        ->get();

        // 3. Inactive customers (haven't ordered in 90 days)
        $inactiveCustomers = Customer::whereHas('orders') // Must have at least one order
            ->whereDoesntHave('orders', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(90));
            })
            ->with('orders') // To get the last order date
            ->take(10)
            ->get();


        return view('admin.reports.customers', compact(
            'topSpenders',
            'mostFrequentBuyers',
            'inactiveCustomers'
        ));
    }

    /**
     * Display the financial report dashboard.
     */
    public function financial(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 1. Calculate sales summary ONLY for 'delivered' orders
        $summary = Order::where('status', 'delivered')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->selectRaw('
                                SUM(total_amount) as total_sales,
                                SUM(total_cost) as total_cogs,
                                SUM(discount_amount) as total_discounts,
                                COUNT(*) as total_orders
                            ')->first();

        // 2. Calculate total expenses
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        // 3. Calculate total purchases
        $totalPurchases = PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total_amount');

        // 4. Calculate final figures
        $grossProfit = $summary->total_sales - $summary->total_cogs;
        $netProfit = $grossProfit - $totalExpenses;
        $averageOrderValue = ($summary->total_orders > 0) ? $summary->total_sales / $summary->total_orders : 0;

        // 5. Data for the chart, ONLY for 'delivered' orders
        $salesByDay = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as sales'),
                DB::raw('SUM(total_cost) as cost'),
            ])
            ->keyBy('date');

        $chartLabels = [];
        $salesData = [];
        $profitData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $chartLabels[] = $formattedDate;

            if (isset($salesByDay[$formattedDate])) {
                $dayData = $salesByDay[$formattedDate];
                $salesData[] = $dayData->sales;
                $profitData[] = $dayData->sales - $dayData->cost;
            } else {
                $salesData[] = 0;
                $profitData[] = 0;
            }
        }

        // 6. Most profitable and best-selling products from 'delivered' orders
        $topProfitableProducts = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'delivered')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity * price) as total_revenue, SUM(cost) as total_cost')
            ->groupBy('product_id')
            ->get()
            ->map(function ($item) {
                $item->profit = $item->total_revenue - $item->total_cost;
                return $item;
            })
            ->sortByDesc('profit')
            ->take(10);
            
        $topSellingProducts = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'delivered')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity_sold')
            ->take(10)
            ->get();

        return view('admin.reports.financial', compact(
            'summary', 'totalExpenses', 'totalPurchases', 'grossProfit', 'netProfit',
            'averageOrderValue', 'topProfitableProducts', 'topSellingProducts', 'year', 'month',
            'chartLabels', 'salesData', 'profitData'
        ));
    }
    public function stockReport(Request $request)
{
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));

    // حسب بياناتك وجداولك، هنا تجيب بيانات المنتجات منخفضة المخزون ونفدت والمنتجات الأكثر مبيعًا
    $lowStockProducts = Product::where('stock_quantity', '<=', 10)->get();
    $outOfStockProducts = Product::where('stock_quantity', '=', 0)->get();
    $topSellingProducts = Product::withCount(['orderItems' => function($q) use($month, $year) {
        $q->whereYear('created_at', $year)
          ->whereMonth('created_at', $month);
    }])->orderByDesc('order_items_count')->take(10)->get();

    return view('admin.reports.stock', compact('month', 'year', 'lowStockProducts', 'outOfStockProducts', 'topSellingProducts'));
}

}
