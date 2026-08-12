<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
     protected $fillable = [
        'product_id',
        'brand',
        'weight',
        'unit',
        'origin',
        'ingredients',
        'nutrition',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public function product(){
        return $this->belongsTo(products::class,'product_id');
    }
}
