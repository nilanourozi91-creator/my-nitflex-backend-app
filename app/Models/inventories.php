<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class inventories extends Model
{
      protected $fillable = [
        'product_id',
        'quantity',
        'low_stock_limit',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'low_stock_limit' =>'integer',
    ];

    public function product(){
     return $this->belongsTo(products::class,'product_id');
    }
}
