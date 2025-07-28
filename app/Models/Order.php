<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // THIS LINE MUST BE PRESENT
use App\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity, SoftDeletes; // AND 'SoftDeletes' MUST BE USED HERE

    protected $fillable = [
        'user_id',
        'customer_id',
        'source',
        'governorate',
        'city',
        'nearest_landmark',
        'notes',
        'total_amount',
        'shipping_cost',
        'status',
        'discount_amount',     // <-- تأكد من وجود هذا السطر
        'discount_code_id',
    ];

    /**
     * العلاقة بين الطلب والمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة بين الطلب والعميل
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * العلاقة بين الطلب وعناصر الطلب (OrderItem)
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * العلاقة بين الطلب والمنتجات عبر جدول order_items
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
                    ->withPivot('quantity', 'price');
    }
public function discountCode()
{
    return $this->belongsTo(DiscountCode::class);
}
public function discountCodeUsage()
{
    return $this->hasOne(DiscountCodeUsage::class);
}
}