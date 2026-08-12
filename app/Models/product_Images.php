<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product_Images extends Model
{
     protected $fillable = [
        'img_url',
    ];
    public function imegeable() {
        return $this->morphTo();
    }
}
