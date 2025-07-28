<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Address;
use App\Services\InventoryService;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdated;

class CheckoutController extends Controller
{
    /**
     * عرض صفحة إتمام الشراء مع جلب كل البيانات اللازمة.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop')->with('info', 'عربة التسوق فارغة.');
        }

        $cartItems = [];
        $subtotal = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->with('firstImage')->get()->keyBy('id');

        foreach ($cart as $id => $details) {
            if (isset($products[$id])) {
                $product = $products[$id];
                $price = $product->current_price;
                $cartItems[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity'],
                    'price' => $price,
                ];
                $subtotal += $price * $details['quantity'];
            }
        }

        $shippingCost = ($subtotal >= 50000) ? 0 : 4000;
        $discountValue = session('discount_value', 0);
        $finalTotal = ($subtotal - $discountValue) + $shippingCost;

        $addresses = Auth::user()->addresses()->latest()->get();

        return view('frontend.checkout.index', compact('cartItems', 'addresses', 'subtotal', 'shippingCost', 'discountValue', 'finalTotal'));
    }

    /**
     * تخزين الطلب الجديد.
     */
public function store(Request $request, InventoryService $inventoryService)
{
    // تحقق من صحة البيانات المدخلة
    $request->validate([
        'saved_address_id' => 'required|exists:addresses,id,user_id,' . Auth::id(),
        'payment_method' => 'required|string',
    ], [
        'saved_address_id.required' => 'يرجى اختيار أو إضافة عنوان شحن للمتابعة.'
    ]);

    // جلب محتويات العربة
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('shop')->with('error', 'عربة التسوق فارغة!');
    }

    DB::beginTransaction();
    try {
        $user = Auth::user();

        // إنشاء أو جلب بيانات العميل
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name, 'phone_number' => $user->phone_number, 'email' => $user->email]
        );

        // حساب المجموع الفرعي
        $subtotal = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        foreach ($cart as $id => $details) {
            if (isset($products[$id])) {
                $product = $products[$id];
                $price = $product->current_price;
                $subtotal += $price * $details['quantity'];
            }
        }

        // جلب العنوان المختار
        $address = Address::findOrFail($request->saved_address_id);

        // حساب تكلفة الشحن (مثال: مجاناً إذا المجموع >= 50,000 وإلا 4,000)
        $shippingCost = ($subtotal >= 50000) ? 0 : 4000;

        // الحصول على قيمة الخصم من الجلسة
        $discountAmount = session('discount_value', 0);
        $discountCodeId = session('discount_code_id', null);

        // حساب المجموع النهائي
        $finalTotal = ($subtotal - $discountAmount) + $shippingCost;

        // إنشاء الطلب مع تخزين قيمة الشحن
        $order = Order::create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'total_amount' => $finalTotal,
            'total_cost' => 0, // سنحدّثها لاحقًا
            'shipping_cost' => $shippingCost,
            'discount_amount' => $discountAmount,
            'discount_code_id' => $discountCodeId,
            'status' => 'pending',
            'governorate' => $address->governorate,
            'city' => $address->city,
            'address_details' => $address->address_details,
            'nearest_landmark' => $address->nearest_landmark,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'payment_method' => $request->payment_method,
        ]);

        // حفظ بنود الطلب وتحديث تكلفة المنتجات الإجمالية
        $totalCost = 0;
        foreach ($cart as $id => $details) {
            if (isset($products[$id])) {
                $product = $products[$id];
                $price = $product->current_price;

                // خصم الكمية من المخزون (حسب دالة الـ InventoryService)
                $itemCost = $inventoryService->deductStock($product, $details['quantity']);

                $totalCost += $itemCost;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $price,
                    'cost' => $itemCost,
                ]);
            }
        }

        // تحديث تكلفة المنتجات الإجمالية في الطلب
        $order->update(['total_cost' => $totalCost]);

        // تسجيل استخدام كود الخصم إن وجد
        if ($discountCodeId) {
            \App\Models\DiscountCodeUsage::create([
                'discount_code_id' => $discountCodeId,
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);
        }

        DB::commit();

        // إشعارات الطلب الجديد
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }
        $user->notify(new OrderStatusUpdated($order));

        // تنظيف بيانات الجلسة المتعلقة بالعربة والخصم
        session()->forget(['cart', 'discount_code', 'discount_value', 'discount_code_id']);

        return redirect()->route('checkout.success')->with('order_id', $order->id);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage());
    }
}

    /**
     * عرض صفحة نجاح الطلب.
     */
    public function success()
    {
        $orderId = session('order_id');
        $order = $orderId ? Order::find($orderId) : null;
        if (!$order) {
            return redirect()->route('homepage');
        }
        return view('frontend.checkout.success', compact('order'));
    }
}