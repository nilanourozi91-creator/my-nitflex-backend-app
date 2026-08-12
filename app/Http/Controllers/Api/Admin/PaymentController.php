<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
       public function index(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->with([
                'order:id,user_id,total_amount,status',
                'order.user:id,name,email',
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
            'data' => $payments,
        ]);
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load([
            'order.items.product',
            'order.user:id,name,email',
        ]);

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    public function updateStatus(
        Request $request,
        Payment $payment
    ): JsonResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                ]),
            ],
        ]);

        $newStatus = $validated['status'];

        if ($payment->status === 'refunded') {
            return response()->json([
                'success' => false,
                'message' => 'A refunded payment cannot be changed.',
            ], 422);
        }

        DB::transaction(function () use (
            $payment,
            $newStatus
        ) {

            $payment->update([
                'status' => $newStatus,

                'transaction_id' =>
                    $newStatus === 'paid'
                        ? ($payment->transaction_id
                            ?? 'FS-' . Str::upper(Str::random(12)))
                        : $payment->transaction_id,

                'paid_at' =>
                    $newStatus === 'paid'
                        ? now()
                        : $payment->paid_at,
            ]);

            if ($newStatus === 'paid') {

                $payment->order()->update([
                    'status' => 'confirmed',
                ]);
            }

            if ($newStatus === 'failed') {

                $payment->order()->update([
                    'status' => 'pending',
                ]);
            }

            if ($newStatus === 'refunded') {

                $payment->order()->update([
                    'status' => 'cancelled',
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
            'data' => $payment->fresh()->load([
                'order.user',
            ]),
        ]);
    }
}
