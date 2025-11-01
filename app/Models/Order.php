<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','status','total_qty','total_amount'];

    public function items() { return $this->hasMany(OrderItem::class); }

    // ทำ alias: $order->total == $order->total_amount
    public function getTotalAttribute()
    {
        return $this->total_amount;
    }
    public function user() { return $this->belongsTo(User::class); }

}


