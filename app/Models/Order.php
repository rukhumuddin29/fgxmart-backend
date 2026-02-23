<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'view_hash',
        'user_id',
        'subtotal',
        'shipping_charges',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'bank_details',
        'notes',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'bank_details' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'desc');
    }
}
