<?php

namespace App\Http\Controllers;

use App\Models\qarse;
use Illuminate\Http\Request;

class QarseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $data=qarse::with(['prodect','user'])->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
