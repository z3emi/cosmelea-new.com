<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\DiscountService;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        $this->middleware($permissionMiddleware . ':view-orders', ['only' => ['index', 'show', 'invoice']]);
        $this->middleware($permissionMiddleware . ':create-orders', ['only' => ['create', 'store', 'applyDiscount']]);
        $this->middleware($permissionMiddleware . ':edit-orders', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware($permissionMiddleware . ':delete-orders', ['only' => ['destroy']]);
        $this->middleware($permissionMiddleware . ':view-trashed-orders', ['only' => ['trash']]);
        $this->middleware($permissionMiddleware . ':restore-orders', ['only' => ['restore']]);
        $this->middleware($permissionMiddleware . ':force-delete-orders', ['only' => ['forceDelete']]);
    }

    /**
     * Display a listing of all orders with filtering.
     */
    public function index(Request $request)
    {
        $query = Order::with('customer', 'user');
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', function ($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', "%{$searchTerm}%")
                           ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_price')) {
            $query->where('total_amount', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('total_amount', '<=', $request->max_price);
        }
        
        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($sortBy === 'user_name') {
            $query->join('customers', 'orders.customer_id', '=', 'customers.id')
                  ->orderBy('customers.name', $sortDir)
                  ->select('orders.*');
        } elseif (in_array($sortBy, ['id', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest('id');
        }

        $perPage = $request->input('per_page', 5);
        $orders = $query->paginate($perPage)->withQueryString();
        
        $users = User::role('Super-Admin')->get();

        $statusLabels = [
            'pending'    => 'قيد الانتظار',
            'processing' => 'قيد التجهيز',
            'shipped'    => 'تم الشحن',
            'delivered'  => 'تم التوصيل',
            'cancelled'  => 'ملغي',
            'returned'   => 'مُرجع',
        ];
        
        return view('admin.orders.index', compact('orders', 'users', 'sortBy', 'sortDir', 'statusLabels'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        
        $products = Product::where('is_active', true)
            ->whereHas('purchaseItems', function ($query) {
                $query->where('quantity_remaining', '>', 0);
            })
            ->get();

        return view('admin.orders.create', compact('customers', 'products'));
    }

    /**
     * Store a new order.
     */
    public function store(Request $request, InventoryService $inventoryService, DiscountService $discountService)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'source' => 'required|string',
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'nearest_landmark' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string',
            'free_shipping' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $items = [];
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("الكمية المطلوبة للمنتج '{$product->name_ar}' غير متوفرة. المتاح: {$product->stock_quantity}");
                }
                $subtotal += $productData['price'] * $productData['quantity'];
                $items[] = [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'price' => $productData['price'],
                    'quantity' => $productData['quantity'],
                ];
            }
            
            $shippingCost = $request->has('free_shipping') ? 0 : 4000;

            $discountAmount = 0;
            $discountCodeId = null;
            if ($request->filled('discount_code')) {
                $result = $discountService->apply($request->discount_code, $items);
                $discountAmount = $result['discount_amount'];
                $discountCodeId = $result['discount_code_id'];
            }
            
            $finalTotal = ($subtotal - $discountAmount) + $shippingCost;

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'source' => $request->source,
                'governorate' => $request->governorate,
                'city' => $request->city,
                'nearest_landmark' => $request->nearest_landmark,
                'notes' => $request->notes,
                'total_amount' => $finalTotal,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'discount_code_id' => $discountCodeId,
                'status' => 'processing',
            ]);

            $totalCost = 0;
            $orderItemsData = [];
            foreach ($request->products as $productId => $productData) {
                $product = Product::find($productId);
                $quantity = $productData['quantity'];
                
                $itemCost = $inventoryService->deductStock($product, $quantity);
                $totalCost += $itemCost;

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $productData['price'],
                    'cost' => $itemCost,
                ];
            }

            $order->items()->createMany($orderItemsData);
            $order->update(['total_cost' => $totalCost]);

            // سجل استخدام كود الخصم إن وجد
            if ($discountCodeId) {
                \App\Models\DiscountCodeUsage::create([
                    'discount_code_id' => $discountCodeId,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                ]);
            }

            DB::commit();

            // إرسال إشعار للإدارة بوجود طلب جديد
            // Notify users with the Super-Admin role
            $admins = User::role('Super-Admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }

            if ($order->customer && $order->customer->user) {
                $order->customer->user->notify(new OrderStatusUpdated($order));
            }

            return redirect()->route('admin.orders.show', $order->id)
                             ->with('success', 'تم إنشاء الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing an order.
     */
    public function edit(Order $order)
    {
        $products = Product::all()->filter(function ($product) use ($order) {
            $stockQuantity = $product->stock_quantity ?? 0;
            return $stockQuantity > 0 || $order->items->contains('product_id', $product->id);
        });

        return view('admin.orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified order in storage, including its items.
     */
    public function update(Request $request, Order $order, InventoryService $inventoryService, DiscountService $discountService)
    {
        $request->validate([
            'governorate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'nearest_landmark' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string',
            'free_shipping' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $order->load('items.product');

            foreach ($order->items as $oldItem) {
                if ($oldItem->product) { 
                    $inventoryService->restoreStock($oldItem->product, $oldItem->quantity);
                }
            }
            $order->items()->delete();

            $subtotal = 0;
            $items = [];
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                $subtotal += $productData['price'] * $productData['quantity'];
                $items[] = [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'price' => $productData['price'],
                    'quantity' => $productData['quantity'],
                ];
            }

            $shippingCost = $request->has('free_shipping') ? 0 : 4000;
            $discountAmount = 0;
            $discountCodeId = null;

            if ($request->filled('discount_code')) {
                $result = $discountService->apply($request->discount_code, $items);
                $discountAmount = $result['discount_amount'];
                $discountCodeId = $result['discount_code_id'];
            }
            
            $finalTotal = ($subtotal - $discountAmount) + $shippingCost;

            $totalCost = 0;
            $newOrderItemsData = [];
            foreach ($request->products as $productId => $productData) {
                $product = Product::find($productId);
                $quantity = $productData['quantity'];
                
                $itemCost = $inventoryService->deductStock($product, $quantity);
                $totalCost += $itemCost;

                $newOrderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $productData['price'],
                    'cost' => $itemCost,
                ];
            }
            $order->items()->createMany($newOrderItemsData);

            $order->update([
                'governorate' => $request->governorate,
                'city' => $request->city,
                'nearest_landmark' => $request->nearest_landmark,
                'notes' => $request->notes,
                'total_amount' => $finalTotal,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'discount_code_id' => $discountCodeId,
                'total_cost' => $totalCost,
            ]);

            // تحديث أو إنشاء سجل استخدام كود الخصم
            if ($discountCodeId) {
                \App\Models\DiscountCodeUsage::create([
                    'discount_code_id' => $discountCodeId,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)->with('success', 'تم تحديث الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $order = Order::withTrashed()
                      ->with([
                          'items.product.firstImage', 
                          'customer.user.addresses',
                          'user', 
                          'discountCode'
                      ])
                      ->findOrFail($id);
                      
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order, InventoryService $inventoryService)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled,returned']);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return redirect()->route('admin.orders.show', $order)->with('info', 'لم تتغير حالة الطلب.');
        }

        DB::beginTransaction();
        try {
            $statusesThatRestoreStock = ['cancelled', 'returned'];

            if (in_array($newStatus, $statusesThatRestoreStock) && !in_array($oldStatus, $statusesThatRestoreStock)) {
                foreach ($order->items as $item) {
                    $inventoryService->restoreStock($item->product, $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);

            if ($order->customer && $order->customer->user) {
                $order->customer->user->notify(new OrderStatusUpdated($order));
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)->with('success', 'تم تحديث حالة الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        if (!in_array($order->status, ['cancelled', 'returned'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف الطلب إلا إذا كانت حالته "ملغي" أو "مرتجع".');
        }

        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'تم نقل الطلب إلى سلة المحذوفات بنجاح.');
    }

    public function trash(Request $request)
    {
        $query = Order::onlyTrashed()->with('customer');
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('customer', function ($subQ) use ($searchTerm) {
                $subQ->where('name', 'like', "%{$searchTerm}%")
                     ->orWhere('phone_number', 'like', "%{$searchTerm}%");
            })->orWhere('id', 'like', "%{$searchTerm}%");
        }
        $trashedOrders = $query->latest()->paginate(10)->withQueryString();
        return view('admin.orders.trash', compact('trashedOrders'));
    }

    public function restore($id)
    {
        Order::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.orders.trash')->with('success', 'تم استعادة الطلب بنجاح.');
    }

    public function forceDelete($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->route('admin.orders.trash')->with('success', 'تم حذف الطلب نهائياً.');
    }

    public function invoice(Order $order)
    {
        $order->load('items.product', 'customer');
        return view('admin.orders.invoice', compact('order'));
    }

    public function applyDiscount(Request $request, DiscountService $discountService)
    {
        $request->validate([
            'code' => 'required|string',
            'items' => 'required|array',
        ]);

        try {
            $result = $discountService->apply($request->code, $request->items);
            return response()->json(['success' => true, 'discount_amount' => $result['discount_amount'], 'message' => 'تم تطبيق الخصم بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
