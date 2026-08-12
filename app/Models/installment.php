<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class installment extends Model
{
    public $fillable = [
        'qars_date'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function prodect(){
        return $this->belongsTo(products::class,'pro_id');
    }
}
