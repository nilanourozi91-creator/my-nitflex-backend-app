<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
      public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => [
                'required',
                'exists:orders,id',
            ],

            'method' => [
                'required',
                'in:cash,card,online',
            ],
        ]);

        $user = $request->user();

        $order = Order::with('payment')
            ->where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if ($order->payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already exists for this order.',
            ], 422);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot pay for a cancelled order.',
            ], 422);
        }

        $payment = DB::transaction(function () use ($order, $validated) {

            $payment = $order->payment()->create([
                'amount' => $order->total_amount,
                'method' => $validated['method'],
                'status' => 'pending',
            ]);

            return $payment;
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment,
        ], 201);
    }
}
