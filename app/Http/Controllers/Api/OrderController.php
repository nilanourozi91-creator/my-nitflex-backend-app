<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
     public function index()
    {
        $orders =Order::with(['user','payment'])->latest()->paginate('10');
        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show( string $id)
    {
       $order =Order::findOrFail($id);
       return response()->json([
        'data'=>$order->load(['user','items','payment']),
       ]);
    }

    // public function store(Request $request,string $id)
    // {
    //     try {
    //           $users=user::findOrFail($id);
    //     $order=$request->validate([
    //        'subtotal'=>'required','string',
    //        'shipping_cost'=>'required','string',
    //        'total_amount'=>'required','string',
    //        'status'=>'required','string',
    //        'shipping_address'=>'required','string',
    //        'notes'=>'required','string',
    //        'phone'=>'required','string',
    //     ]);
    //     $user=$request->user();
    //   $cart=$user->cart();
    //     if ($cart !== $users->cart() && $cart->items->isEmpty()) {
    //         throw ValidationException::withMessages([
    //                 'cart' => ['Your cart is empty.'],
    //              ]);
    //     }

    //       $subtotal = 0;

    //          foreach ($cart->items as $item) {

    //             $product = $item->product;

    //             if (!$product || !$product->is_active) {
    //                  throw ValidationException::withMessages([
    //                     'product' => [
    //                      "Product {$item->product_id} is no longer available."
    //                      ],
    //                  ]);
    //              }

    //              $inventory = $product->inventory;

    //              if (!$inventory) {
    //                  throw ValidationException::withMessages([
    //                      'inventory' => [
    //                          "Inventory not found for {$product->name}."
    //                     ],
    //                  ]);
    //              }

    //              if ($inventory->quantity < $item->quantity) {
    //                  throw ValidationException::withMessages([
    //                      'stock' => [
    //                          "{$product->name} does not have enough stock."
    //                      ],
    //                  ]);
    //              }

    //              $subtotal += $item->price * $item->quantity;
    //          }

    //         $shippingCost = $subtotal >= 100 ? 0 : 5;

    //          $totalAmount = $subtotal + $shippingCost;

    //         $order = $user->orders()->create([
    //            'subtotal' => $subtotal,
    //              'shipping_cost' => $shippingCost,
    //              'total_amount' => $totalAmount,
    //             'status' => 'pending',
    //             'phone'=>$request->phone,
    //             'shipping_address' => $request->shipping_address,
    //              'notes' => $request->notes ?? null,
    //          ]);

    //          foreach ($cart->items as $item) {

    //              $product = $item->product;

    //              $order->items()->create([
    //                  'product_id' => $product->id,
    //                  'product_name' => $product->name,
    //                  'price' => $item->price,
    //                  'quantity' => $item->quantity,
    //                  'subtotal' => $item->price * $item->quantity,
    //              ]);

    //              $product->inventory()->decrement(
    //                  'quantity',
    //                  $item->quantity
    //              );
    //          }
    //          $cart->items()->delete();
    //          return $order;
    //      $order->load([
    //          'items.product',
    //         'payment',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order created successfully.',
    //         'data' => $order,
    //     ], 201);
    //     } catch (Exception $error) {
    //         return response([
    //             'massege'=>$error->getMessage(),
    //         ]);
    //     }
       
    // }
  

    // }

    public function store(Request $request)
{
    try {

        $request->validate([
            'shipping_address' => 'required|string',
            'phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty.'],
            ]);
        }

        $subtotal = 0;

        foreach ($cart->items as $item) {

            $product = $item->product;

            if (!$product || !$product->is_active) {
                throw ValidationException::withMessages([
                    'product' => [
                        "Product {$item->product_id} is no longer available."
                    ],
                ]);
            }

            $inventory = $product->inventory;

            if (!$inventory) {
                throw ValidationException::withMessages([
                    'inventory' => [
                        "Inventory not found for {$product->name}."
                    ],
                ]);
            }

            if ($inventory->quantity < $item->quantity) {
                throw ValidationException::withMessages([
                    'stock' => [
                        "{$product->name} does not have enough stock."
                    ],
                ]);
            }

            $subtotal += $item->price * $item->quantity;
        }

        $shippingCost = $subtotal >= 100 ? 0 : 5;

        $totalAmount = $subtotal + $shippingCost;

        $order = $user->orders()->create([
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'phone' => $request->phone,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ]);

        foreach ($cart->items as $item) {

            $product = $item->product;

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->price * $item->quantity,
            ]);

            $product->inventory()->decrement(
                'quantity',
                $item->quantity
            );
        }

        $cart->items()->delete();

        $order->load([
            'items.product',
            'payment',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);

    } catch (Exception $error) {

        return response()->json([
            'success' => false,
            'message' => $error->getMessage(),
        ], 500);
    }
}
}
    

