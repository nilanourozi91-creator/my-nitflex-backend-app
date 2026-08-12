<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
     public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $order->load([
            'items.product',
            'payment',
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => [
                'required',
                'string',
                'max:500',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $validated) {

            $cart = $user->cart()
                ->with('items.product.inventory')
                ->first();

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
                'shipping_address' => $validated['shipping_address'],
                'phone' => $validated['phone'],
                'notes' => $validated['notes'] ?? null,
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

            return $order;
        });

        $order->load([
            'items.product',
            'payment',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
