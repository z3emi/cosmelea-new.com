<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'name_ku',
        'sku',
        'description_ar',
        'description_en',
        'description_ku',
        'price',
        'sale_price',
        'image_url',
        'is_active', // <-- تمت الإضافة هنا
    ];

    /**
     * العلاقة مع وجبات الشراء
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
    
    /**
     * Accessor لحساب المخزون الحالي من مجموع الكميات المتبقية في كل وجبة
     */
    public function getStockQuantityAttribute()
    {
        // يجمع الكميات المتبقية من كل وجبات الشراء لهذا المنتج
        return $this->purchaseItems()->sum('quantity_remaining');
    }

    /**
     * Accessor جديد للحصول على تكلفة آخر وجبة شراء
     */
    public function getLastPurchaseCostAttribute()
    {
        // جلب آخر وجبة شراء لهذا المنتج بناءً على تاريخ الإنشاء تنازليًا
        $latestBatch = $this->purchaseItems()->latest()->first();

        // إذا وجدت وجبة، أرجع سعر الشراء الخاص بها، وإلا أرجع صفر
        return $latestBatch ? $latestBatch->purchase_price : 0;
    }

    // =======================================================
    // باقي العلاقات والدوال في المودل تبقى كما هي
    // =======================================================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function orderItems()
    {
        // تأكد من أن اسم المودل هو OrderItem أو ما يتوافق مع مشروعك
        return $this->hasMany(OrderItem::class);    
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
                     ->withPivot('quantity', 'price');
    }

    public function isFavorited()
    {
        return auth()->check() && $this->favorites()->where('user_id', auth()->id())->exists();
    }
        public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Relationship to get just the first image (useful for display).
     */
    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }
}