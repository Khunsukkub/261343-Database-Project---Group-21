<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'user_id',      
        'product_id',   
        'qty',          
        'price',        
        'selected', 
        'checked_out_at',    
    ];

    protected $casts = [
        'qty'        => 'integer',
        'price'      => 'decimal:2',
        'selected'   => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    // ความสัมพันธ์ที่พบบ่อย
    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }

    // คำนวณราคารวมต่อแถว
    public function getLineTotalAttribute()
    {
        return $this->qty * $this->price;
    }

    // scope ช่วยดึงเฉพาะที่ถูกเลือก
    public function scopeSelected($q) { return $q->where('selected', true); }

    public function scopeActive($q) {
        return $q->whereNull('checked_out_at');
    }
}
