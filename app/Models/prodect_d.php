<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prodect_d extends Model
{
    public $fillable = [
        'Amount',
        'price',
        'stock',
    ];

    public function prodect(){
        $this->belongsTo(prodect::class,'pro_id');
    }
}
