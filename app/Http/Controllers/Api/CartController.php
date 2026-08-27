<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\products;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $cart =Cart::with(['user','items'])->get();
        return response()->json([
            'success' => true,
            'data' => $cart,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
      public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart($request);

        $product = products::with('inventory')
            ->where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $availableQuantity = $product->inventory?->quantity ?? 0;

        if ($availableQuantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $validated['quantity'];

            if ($newQuantity > $availableQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity exceeds available stock.',
                ], 422);
            }

            $item->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $item = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data' => $cart->fresh()->load('items.product'),
        ], 201);
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
     public function update(
        Request $request,
        int $item
    ) {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart($request);

        $cartItem = $cart->items()
            ->with('product.inventory')
            ->findOrFail($item);

        $availableQuantity = $cartItem->product->inventory?->quantity ?? 0;

        if ($validated['quantity'] > $availableQuantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully.',
            'data' => $cart->fresh()->load('items.product'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        int $item
    ) {
        $cart = $this->getCart($request);

        $cartItem = $cart->items()->findOrFail($item);

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart.',
            'data' => $cart->fresh()->load('items.product'),
        ]);
    }

      public function clear(Request $request)
    {
        $cart = $this->getCart($request);

        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.',
        ]);

    }
        private function getCart(Request $request)
       {
        return $request->user()->cart()->firstOrCreate([]);
    }   
}

