<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Apply permission middleware to protect controller actions.
     */
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;

        $this->middleware($permissionMiddleware . ':view-products', ['only' => ['index', 'show']]);
        $this->middleware($permissionMiddleware . ':create-products', ['only' => ['create', 'store']]);
        $this->middleware($permissionMiddleware . ':edit-products', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware($permissionMiddleware . ':delete-products', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load the first image for each product and allow custom per_page value
        $perPage = request('per_page', 5);
        $products = Product::with('firstImage')->latest()->paginate($perPage);

        return view('admin.products.index', compact('products'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            // ... other validations
            'images' => 'required|array', // Validate that 'images' is an array
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validate each image
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($request->except('images'));

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج وصوره بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            // ... other validations
            'images' => 'nullable|array', // Images are not required on update
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $product->update($request->except('images'));

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح.');
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $message = $product->is_active ? 'تم تفعيل المنتج بنجاح.' : 'تم إيقاف المنتج بنجاح.';
        return redirect()->route('admin.products.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // ... check for orders remains the same ...

        // Delete all associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $product->delete(); // This will also delete the image records due to onDelete('cascade')
        
        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج وجميع صوره بنجاح.');
    }
    public function destroyImage(ProductImage $image)
    {
        // التأكد من أن المستخدم يمتلك صلاحية التعديل
        $this->authorize('edit-products');

        // حذف ملف الصورة من مجلد التخزين
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // حذف سجل الصورة من قاعدة البيانات
        $image->delete();

        // إرجاع استجابة نجاح للطلب
        return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح.']);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }
}
