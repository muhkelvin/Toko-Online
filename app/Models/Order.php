<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
    ];

    /**
     * Relasi ke User: Satu order dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke OrderItem: Satu order memiliki banyak item.
     * Nama method disesuaikan menjadi 'orderItems' untuk mengatasi error.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Payment: Satu order memiliki satu pembayaran.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
