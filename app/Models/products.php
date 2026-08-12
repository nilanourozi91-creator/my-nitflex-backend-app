<?php

namespace App\Models;

use App\Http\Controllers\ProductImagesController;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
        protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(){
        return $this->belongsTo(categories::class,'category_id');
    }
    public function reviews(){
        return $this->hasMany(review::class,'pro_id');
    }
    public function details(){
    return $this->hasOne(ProductDetail::class,'product_id');
   }
     public function inventory(){
        return $this->hasOne(inventories::class,'product_id');
    }
    public function cartItems(){
    return $this->hasMany(CartItem::class,'product_id');
}
    public function imgall(){
        return $this->morphMany(product_Images::class,'imegeable');
    }
}
