<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\prodectRequest;
use App\Models\products;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //  $products=products::with(['category','details','inventory','imgall' ])->where('is_active', true)->latest()->paginate(12);
        $products=products::with(['imgall','category','inventory','details','reviews'])->latest()->get();
        // $products=products::all();
        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
      try {
          $product = products::create([
            'category_id'=> $request->category_id,
            'name'=> $request->name,
            'slug'=>$request->name,
            'description'=> $request->description,
            'price'=> $request->price,
            'is_active' => $request->is_active,
        ]);

        $product->details()->create([
            'brand' => $request->brand,
            'weight' => $request->weight,
            'unit' => $request->unit,
            'origin' => $request->origin,
            'ingredients' => $request->ingredients,
            'nutrition' => $request->nutrition,
        ]);
        $product->inventory()->create([
            'quantity' => $request->	quantity,
            'low_stock_limit' => $request->low_stock_limit,
        ]);

        $images = [];
    if ($request->hasFile('img1')) {
        $images[] = ['img_url' => $request->file('img1')->store('prodect_img','public')];
    }
    if ($request->hasFile('img2')) {
        $images[] = ['img_url' => $request->file('img2')->store('prodect_img','public')];
    }
    if (!empty($images)) {
        $product->imgall()->createMany($images);
    }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product->load(['category','details','inventory','imgall',]),
        ], 201);
      } catch (Exception $error) {
        return $error->getMessage();
      }
    }

    /**
     * Display the specified resource.
     */
     public function show(string $id)
    {
        $products=products::FindOrFail($id);
        return response()->json([
            'data'=>$products->load([
            'imgall','category','inventory','details','reviews'
            ]),
        ]);
       
    }
    /**
     * Update the specified resource in storage.
     */
     public function update(prodectRequest $request, string $id)
    {
        try {
            
        } catch (Exception $error) {
            return response()->json([
                'massege'=>$error->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
      public function destroy(string $id)
    {  
          $pro=products::findOrFail($id);
    //    $pro->load('prodectD',' imgall');
       $pro->delete();
       $pro->details()->delete();
       $pro->inventory()->delete();
       foreach ($pro->imgall as $img) {
        if(Storage::disk('public')->exists($img->img_url)){
          Storage::disk('public')->delete($img->img_url);
        }
       
       }    
        $pro->imgall()->delete();
        return response()->json([
            'data'=>$pro,
            'massege'=>'prodeactdeleted secssafuly'
        ]);
    }
     public function getlatestprodect(){
       $all =products::where('created_at','<=',Carbon::now()->subDays(30))->OrWhere('created_at','=>',Carbon::now()->subDays(60))->count();
         return response()->json([
            'data'=>$all
         ]);      
    }
    // get all user
    public function CurrentProdect(){
       $all =products::where('created_at','<=',now())->OrWhere('created_at','>=',Carbon::now()->subDays(30))->count();
         return response()->json([
            'data'=>$all
         ]);      
    }
     

    public function GetData() {
        $products=products::all();
        return response()->json([
            'data' => $products->load(['imgall','details']),
        ]);
    }
}
