<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class DiscountCode extends Model
{
    use HasFactory, LogsActivity;
    use HasFactory;
    
    /**
     * تم إضافة 'max_discount_amount' هنا لحل المشكلة
     */
    protected $fillable = [
        'code', 
        'type', 
        'value', 
        'max_discount_amount', // <-- هذا هو السطر المهم الذي تم إضافته
        'expires_at', 
        'max_uses', 
        'max_uses_per_user', 
        'is_active'
    ];

    protected $casts = ['expires_at' => 'datetime'];

    // العلاقة بين الكودات وسجلات الاستخدام
    public function usages()
    {
        return $this->hasMany(DiscountCodeUsage::class, 'discount_code_id');
    }

    /**
     * المنتجات المرتبطة بهذا الكود.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_code_product');
    }

    /**
     * الأقسام المرتبطة بهذا الكود.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_discount_code');
    }
}
