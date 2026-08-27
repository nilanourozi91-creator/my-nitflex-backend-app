<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Exception;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(string $id) {
     // $user=User::findOrFail($id);
     //       try {
     //              $wishlist = Wishlist::firstOrCreate([ 'user_id'=>$user]);
     //     $wishlist->load([ 'items.product', ]);
         
     //    return response()->json
     //    ([ 'success' => true, 'data' => $wishlist, ]);

     //       } catch (Exception $error) {
     //           return response()->json([
     //                'data'=>$error->getMessage(),
     //           ]);
     //       }

     //     }

    $user = User::findOrFail($id);

    try {

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $wishlist->load([
            'items.product',
        ]);

        return response()->json([
            'success' => true,
            'data' => $wishlist,
        ]);

    } catch (Exception $error) {

        return response()->json([
            'success' => false,
            'message' => $error->getMessage(),
        ], 500);
    }
      /** * Add product to wishlist */ 
        public function store(Request $request) {

        $validated = $request->validate([
        'product_id' => [ 'required', 'integer', 'exists:products,id', ],
         ]);
        $wishlist = Wishlist::firstOrCreate([
             'user_id' => 
             $request->user()->id,
              ]);

         $item = WishlistItem::firstOrCreate([ 'wishlist_id' => $wishlist->id,
        'product_id' => $validated['product_id'], ]); $item->load('product');
        return response()->json([ 
        'success' => true, 'message' =>
        'Product added to wishlist.', 'data'
        => $item, ], 201); }

        /** * Remove product from wishlist */
         public function destroy( Request $request, int $productId)
          { $wishlist = Wishlist::where(
        'user_id', $request->user()->id )->first();
         if (!$wishlist)
         { return response()->json(
          [ 'success' => false, 'message' => 'Wishlist not found.', ]
         , 404); }
         $deleted = WishlistItem::where(
         'wishlist_id', $wishlist->id )
        ->where( 'product_id', $productId )
        ->delete();
        if (!$deleted) {
        return response()->json(
        [ 'success' => false, 'message' => 'Product is not in wishlist.', ],
         404); } return response()->json([
         'success' => true, 'message' => 
         'Product removed from wishlist.', ]);
          }
}
