<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prodect extends Model
{
        protected $fillable = [
          'name',
          'price',
          'stock',
      ];
    
    public function user(){
       return $this->belongsTo(User::class,'user_id');
    }
    public function prodect_d() {
        return $this->hasOne(prodect_d::class,'pro_id');
    }
}
