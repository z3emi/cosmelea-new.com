<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DiscountService;

class CartController extends Controller
{
    /**
     * عرض صفحة السلة مع جلب بيانات المنتجات الكاملة وحساب الشحن.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        if (!empty($cart)) {
            $productIds = array_keys($cart);
            
            // جلب كل المنتجات الموجودة في السلة مع صورها الأولى دفعة واحدة
            $products = Product::whereIn('id', $productIds)->with('firstImage')->get()->keyBy('id');

            foreach ($cart as $id => $details) {
                if (isset($products[$id])) {
                    $product = $products[$id];
                    $cartItems[$id] = [
                        'product' => $product,
                        'quantity' => $details['quantity'],
                    ];
                    $total += $product->price * $details['quantity'];
                } else {
                    // إذا كان المنتج في السلة ولكنه غير موجود في قاعدة البيانات، قم بإزالته
                    unset($cart[$id]);
                    session()->put('cart', $cart);
                }
            }
        }

        $shippingCost = ($total >= 50000) ? 0 : 4000;
        $discountValue = session()->get('discount_value', 0);
        $finalTotal = ($total - $discountValue) + $shippingCost;

        return view('frontend.cart.index', compact('cartItems', 'total', 'discountValue', 'finalTotal', 'shippingCost'));
    }

    /**
     * إضافة منتج إلى السلة
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            // نقوم بتخزين الكمية فقط، لأن بقية البيانات سيتم جلبها من قاعدة البيانات
            $cart[$product->id] = [
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المنتج إلى السلة.',
                'cartCount' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return redirect()->back()->with('success', 'تمت إضافة المنتج إلى السلة.');
    }

    /**
     * تحديث كمية منتج في السلة (مع إرجاع JSON)
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        // إرجاع استجابة JSON للتحديث الفوري
        return response()->json([
            'success' => true,
            'cartCount' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * إزالة منتج من السلة (مع إرجاع JSON)
     */
    public function destroy(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        // إرجاع استجابة JSON للتحديث الفوري
        return response()->json([
            'success' => true,
            'cartCount' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * تطبيق كود خصم باستخدام DiscountService (تم تحسينه لـ AJAX).
     */
    public function applyDiscount(Request $request, DiscountService $discountService)
    {
        $request->validate(['discount_code' => 'required|string']);

        $cart = session()->get('cart', []);
        $total = 0;
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $id => $details) {
            if (isset($products[$id])) {
                $total += $products[$id]->price * $details['quantity'];
            }
        }

        try {
            $result = $discountService->apply($request->discount_code, $total);
            session([
                'discount_code' => $request->discount_code,
                'discount_value' => $result['discount_amount'],
            ]);

            // إرجاع استجابة JSON
            return response()->json([
                'success' => true,
                'message' => 'تم تطبيق كود الخصم بنجاح.',
                'discount_value' => $result['discount_amount'],
                'discount_code' => $request->discount_code
            ]);

        } catch (\Exception $e) {
            session()->forget(['discount_code', 'discount_value']);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * إزالة كوبون الخصم من السلة.
     */
    public function removeDiscount(Request $request)
    {
        session()->forget(['discount_code', 'discount_value']);

        return response()->json([
            'success' => true,
            'message' => 'تمت إزالة كوبون الخصم.'
        ]);
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        return response()->json(['count' => $count]);
    }

    public function content()
    {
        return response()->json(session()->get('cart', []));
    }
        public static function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
    // ===== END: الدالة المضافة =====
}
