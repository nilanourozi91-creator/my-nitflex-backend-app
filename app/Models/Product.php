<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $fillable = [
        'price',
        'stock',
        'name',
    ];
     public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function prodectd(){
        return $this->hasOneThrough(product_detail::class,'pro_id');
    }
}
