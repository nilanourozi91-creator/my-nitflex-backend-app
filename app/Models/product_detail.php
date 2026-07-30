<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product_detail extends Model
{
    public $fillable = [
        'amount',
    ];
    public function prodect(){
        return $this->belongsTo(Product::class,'pro_id');
    }
}
