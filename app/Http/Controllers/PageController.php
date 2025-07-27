<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    /**
     * Display the homepage with all required sections and variables.
     */
    public function homepage()
    {
        // Get 14 new products
        $newProducts = Product::where('is_active', true)
            ->with('firstImage')
            ->latest()
            ->take(14)
            ->get();

        // Get 14 products on sale
        $saleProducts = Product::where('is_active', true)
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->with('firstImage')
            ->inRandomOrder()
            ->take(14)
            ->get();

        // Get 14 best-selling products
        $bestSellingProducts = Product::where('is_active', true)
            ->with('firstImage')
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(14)
            ->get();

        // Get parent categories
        $categories = Category::whereNull('parent_id')->latest()->take(6)->get();

        // Get the user's favorite product IDs (if logged in)
        $favoriteProductIds = [];
        if (Auth::check()) {
            $favoriteProductIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }
        

        return view('frontend.homepage', compact(
            'newProducts',
            'saleProducts',
            'bestSellingProducts',
            'categories',
            'favoriteProductIds'
        ));
    }

    /**
     * Shop page with advanced filtering.
     */
    public function shop(Request $request)
    {
        // Start query with active products and first image
        $query = Product::where('is_active', true)->with('firstImage');

        // Search filter
        if ($q = $request->input('q')) {
            $query->where('name_ar', 'like', "%{$q}%");
        }

        // Price filter
        if ($min = $request->input('min_price')) {
            $query->where('price', '>=', $min);
        }
        if ($max = $request->input('max_price')) {
            $query->where('price', '<=', $max);
        }

        // Hierarchical category filter
        if ($slug = $request->input('category')) {
            $category = Category::where('slug', $slug)->with('children')->first();
            if ($category) {
                // If it's a parent category with children, include their products too
                if ($category->parent_id === null && $category->children->isNotEmpty()) {
                    $categoryIds = $category->children->pluck('id')->push($category->id);
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->where('category_id', $category->id);
                }
            }
        }

        // On-sale filter
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')->where('sale_price', '>', 0);
        }

        // Get products with sorting and pagination
        $products = $query->latest()->paginate(21)->withQueryString(); // 21 products = 3 rows * 7 products

        // Get categories for display in filters
        $categories = Category::whereNull('parent_id')->with('children')->get();

        $pageTitle = 'المتجر';
        if ($request->filled('category') && isset($category)) {
            $pageTitle = $category->name_ar;
        } elseif ($request->filled('q')) {
            $pageTitle = 'نتائج البحث عن: "' . e($request->q) . '"';
        }

        $favoriteProductIds = Auth::check() ? Auth::user()->favorites()->pluck('product_id')->toArray() : [];

        return view('frontend.shop', compact('products', 'categories', 'pageTitle', 'favoriteProductIds'));
    }

    /**
     * Product detail page.
     */
    public function productDetail(Product $product)
    {
        // Prevent access to inactive products
        if (!$product->is_active) {
            abort(404);
        }

        // Load all images for the main product
        $product->load('images');

        // Get related products intelligently
        $relatedProducts = Product::where('is_active', true)
            ->with('firstImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $needed = 4 - $relatedProducts->count();

        if ($needed > 0) {
            // If we didn't find 4 products, complete the list with random products from other categories
            $productIdsToExclude = $relatedProducts->pluck('id')->push($product->id);

            $fallbackProducts = Product::where('is_active', true)
                ->with('firstImage')
                ->whereNotIn('id', $productIdsToExclude)
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $relatedProducts = $relatedProducts->merge($fallbackProducts);
        }

        $isFavorited = Auth::check() ? Auth::user()->favorites()->where('product_id', $product->id)->exists() : false;

        $favoriteProductIds = Auth::check() ? Auth::user()->favorites()->pluck('product_id')->toArray() : [];

        return view('frontend.product-detail', compact('product', 'relatedProducts', 'isFavorited', 'favoriteProductIds'));
    }

    /**
     * Privacy Policy page.
     */
    public function privacyPolicy()
    {
        return view('frontend.pages.privacy-policy');
    }

    /**
     * Categories page.
     */
    public function categories()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->withCount('products')
            ->get();

        return view('frontend.pages.categories', compact('categories'));
    }
}