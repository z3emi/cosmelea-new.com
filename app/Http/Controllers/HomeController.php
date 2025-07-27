<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // جلب كل التصنيفات
        $categories = Category::all();

        // جلب آخر 8 منتجات فقط لعرضها كمميزة (بدون استخدام is_featured)
        $featuredProducts = Product::latest()->take(8)->get();

        // إرسال البيانات إلى واجهة العرض
        return view('frontend.homepage', compact('categories', 'featuredProducts'));
    }
}
