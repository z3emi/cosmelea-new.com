<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name_ar',
        'slug',
        'image',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children')->withCount('products');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * دالة جديدة لحساب المجموع الكلي للمنتجات بشكل متداخل (Recursive).
     * تقوم بجمع عدد منتجات القسم الحالي مع عدد منتجات كل الأقسام الفرعية.
     */
    public function getTotalProductsCountAttribute()
    {
        // ابدأ بعدد المنتجات الموجودة مباشرة في هذا القسم
        // a 'products_count' attribute is automatically added by the withCount('products') method
        $count = $this->products_count;

        // قم بإضافة عدد المنتجات من كل الأقسام الفرعية
        foreach ($this->children as $child) {
            // استدعاء نفس الدالة على القسم الفرعي لجمع العدد منه ومن توابعه
            $count += $child->total_products_count;
        }

        return $count;
    }
}
