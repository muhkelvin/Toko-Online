<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'inventory','image','categories_id','is_visible'];

    // Satu produk dapat muncul di banyak cart item dan order item
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category()
    {
        // Secara eksplisit mendefinisikan foreign key yang benar
        // Argumen kedua pada belongsTo adalah nama foreign key
        return $this->belongsTo(Category::class, 'categories_id');
    }
}
