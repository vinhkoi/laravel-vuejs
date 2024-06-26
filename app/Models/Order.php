<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['total_price', 'status', 'session_id', 'user_address_id',  'created_by', 'updated_by','shipping_fee',
    'estimated_delivery_time',];
    use HasFactory;
    function order_items()  {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function user_address()
    {
        return $this->belongsTo(UserAddress::class);
    }

}
