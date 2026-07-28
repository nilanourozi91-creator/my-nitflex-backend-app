<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class qarse extends Model
{
    public $fillable = [
        'qase_date'
    ];
     public function user(){
     return $this->belongsTo(User::class,'user_id');
   }
     public function prodect(){
     return $this->belongsTo(prodect::class,'pro_id');
   }
}
