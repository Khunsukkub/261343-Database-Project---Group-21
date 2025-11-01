<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','image_path','description','stock'];

    protected $casts = [
        'price'      => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'stock'=>'integer',
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return 'https://picsum.photos/seed/'.$this->id.'/600/450';
        }
        return asset('storage/'.$this->image_path);
    }
}
