<?php

namespace App\Http\Controllers;

use App\Models\prodect;
use Illuminate\Http\Request;

class ProdectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $pro= prodect::with(['prodect_d','user'])->paginate(10);
        return response()->json([
        'data'=>$pro
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $prodects=prodect::create([
            'name'=>$request->name,
            'stock'=>$request->stock,
            'price'=>$request->price,
        ]);
         $prodects->prodect_d()->create([
            'amount'=>$request->amount,
            'catagory'=>$request->catagory,
            'brand'=>$request->brand,
            'description'=>$request->description,
         ]);
         return response()->json([
            'data'=>$prodects,
            'success'=>true,
         ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
