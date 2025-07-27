<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'type',
        'banned_at',
        'governorate',
        'city',
        'address',
        'latitude',
        'longitude',
        // -- الحقول الجديدة للتحقق عبر واتساب --
        'whatsapp_otp',
        'whatsapp_otp_expires_at',
        'phone_verified_at',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'whatsapp_otp', // إخفاء الرمز من أي استجابة API
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'banned_at' => 'datetime',
        // -- تحويل الحقول الجديدة إلى كائنات تاريخ --
        'whatsapp_otp_expires_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * ===== START: تم تصحيح هذه الدالة =====
     * Get the orders for the user.
     */
    public function orders()
    {
        // The relationship should be based on the 'user_id' foreign key in the 'orders' table.
        return $this->hasMany(Order::class, 'user_id');
    }
    // ===== END: تم تصحيح هذه الدالة =====

    /**
     * Get the user's favorites.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    /**
     * Check if the user has favorited a product.
     */
    public function hasFavorited($product)
    {
        return $this->favorites()
            ->where('product_id', $product->id)
            ->exists();
    }

    /**
     * Get the user's addresses.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
