<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // ... دالة __construct() تبقى كما هي ...
    public function __construct()
    {
        $permissionMiddleware = \Spatie\Permission\Middleware\PermissionMiddleware::class;
        $this->middleware($permissionMiddleware . ':view-categories', ['only' => ['index']]);
        $this->middleware($permissionMiddleware . ':create-categories', ['only' => ['create', 'store']]);
        $this->middleware($permissionMiddleware . ':edit-categories', ['only' => ['edit', 'update']]);
        $this->middleware($permissionMiddleware . ':delete-categories', ['only' => ['destroy']]);
    }

public function index()
    {
        $categories = Category::whereNull('parent_id')
                              ->with('children')
                              ->withCount('products') // <-- تأكد من وجود هذا السطر
                              ->latest()
                              ->get();
                              
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // جلب كل الأقسام لتكون متاحة كقسم أب
        $parentCategories = Category::all();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:categories',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id' // التحقق من وجود القسم الأب
        ]);

        $path = $request->file('image')->store('categories', 'public');

        Category::create([
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name_ar),
            'image' => $path,
            'parent_id' => $request->parent_id, // حفظ القسم الأب
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'تم إنشاء القسم بنجاح.');
    }

    public function edit(Category $category)
    {
        // جلب كل الأقسام ما عدا القسم الحالي لمنع اختياره كأب لنفسه
        $parentCategories = Category::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255|unique:categories,name_ar,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $data = [
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name_ar),
            'parent_id' => $request->parent_id, // تحديث القسم الأب
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث القسم بنجاح.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'تم حذف القسم بنجاح.');
    }
}
