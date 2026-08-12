<?php

namespace App\Http\Controllers;

use App\Models\products;
use Illuminate\Http\Request;

class PouplerProdects extends Controller
{
     public function index()
    {
           $products=products::all();
        return response()->json([
            'data'=>$products->load([
            'category',
            'details',
            'inventory',
            'imgall',
            'reviews'
            ]),
        ]);
    }
}
