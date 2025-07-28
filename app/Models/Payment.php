<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'payment_method', 'payment_status', 'amount','payment_proof'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function getPaymentProofUrlAttribute()
    {
        return $this->payment_proof ? Storage::url($this->payment_proof) : null;
    }
}
