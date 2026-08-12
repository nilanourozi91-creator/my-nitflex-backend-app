<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
      protected $fillable = [
        'user_id',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'status',
        'shipping_address',
        'phone',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
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

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
//     اگر نمی‌خواهی از Str استفاده کنی

// می‌توانی فعلاً slug را دستی بسازی، ولی پیشنهاد نمی‌کنم:

// 'slug' => strtolower(str_replace(' ', '-', $validated['name'])),
    
}
