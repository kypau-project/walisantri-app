<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * List all payments
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $payments = $student->payments()
            ->with('bill')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    /**
     * Show payment detail
     */
    public function show(Request $request, $id): JsonResponse
    {
        $student = $request->user()->student;

        $payment = $student->payments()
            ->with('bill')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment),
        ]);
    }
}
