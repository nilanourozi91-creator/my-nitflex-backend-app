<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
     protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(){
        return $this->hasMany(products::class,'category_id');
    }
}
