<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
      public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with([
                'user:id,name,email',
                'items.product:id,name',
                'payment',
            ])
            ->when(
                $request->status,
                function ($query, $status) {
                    $query->where('status', $status);
                }
            )
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load([
            'user:id,name,email',
            'items.product',
            'payment',
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function updateStatus(
        Request $request,
        Order $order
    ): JsonResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                    'cancelled',
                ]),
            ],
        ]);

        $newStatus = $validated['status'];

        if (
            $order->status === 'delivered' &&
            $newStatus !== 'delivered'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'A delivered order cannot be changed.',
            ], 422);
        }

        if (
            $order->status === 'cancelled' &&
            $newStatus !== 'cancelled'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'A cancelled order cannot be reopened.',
            ], 422);
        }

        $order->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => $order->fresh()->load([
                'user:id,name,email',
                'items.product',
                'payment',
            ]),
        ]);
}
}