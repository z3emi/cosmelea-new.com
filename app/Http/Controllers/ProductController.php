<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * البحث عن المنتجات وعرض النتائج.
     */
    public function search(Request $request)
{
    // الحصول على كلمة البحث من الطلب
    $query = $request->input('query');

    // جلب الفئات الرئيسية مع الأبناء
    $categories = \App\Models\Category::with('children')->whereNull('parent_id')->get();

    // البدء ببناء استعلام المنتجات
    $productsQuery = Product::query();

    // تطبيق فلتر البحث فقط إذا كانت هناك كلمة بحث
    if ($query) {
        $productsQuery->where(function ($q) use ($query) {
            $q->where('name_ar', 'LIKE', "%{$query}%")
              ->orWhere('name_en', 'LIKE', "%{$query}%")
              ->orWhere('description_ar', 'LIKE', "%{$query}%");
        });
    }

    // جلب المنتجات مع تقسيم الصفحات
    $products = $productsQuery->latest()->paginate(16);

    // إضافة كلمة البحث إلى روابط الصفحات
    $products->appends(['query' => $query]);

    // إرسال البيانات إلى الواجهة مع $categories
    return view('frontend.shop', [
        'products' => $products,
        'pageTitle' => 'نتائج البحث عن: "' . e($query) . '"',
        'searchQuery' => $query, // لإبقاء كلمة البحث في شريط البحث
        'categories' => $categories, // << مهم هنا
    ]);
}}