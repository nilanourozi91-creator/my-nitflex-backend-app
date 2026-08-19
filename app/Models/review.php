<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    public function products(){
        return $this->hasMany(products::class,'pro_id');
    }
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
