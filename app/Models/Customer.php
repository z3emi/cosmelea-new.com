<?php
// ======================================================================
// الملف: app/Models/Customer.php (محدث)
// ======================================================================
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;
    use HasFactory;

    /**
     * تم إضافة user_id هنا
     */
    protected $fillable = [
        'user_id', 
        'name', 
        'phone_number', 
        'email', 
        'governorate', 
        'city', 
        'address_details',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function addresses()
{
    return $this->hasMany(\App\Models\Address::class);
}
}
