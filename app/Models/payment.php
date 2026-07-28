<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    public $fillable = [
       'pay_date',
   ];

   public function prodect(){
     return $this->belongsTo(prodect::class,'pro_id');
   }
   public function qarse(){
     return $this->belongsTo(qarse::class,'qars_id');
   }
   public function user(){
     return $this->belongsTo(User::class,'user_id');
   }
}
