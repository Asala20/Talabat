<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display all payments.
     */
    public function index()
    {
        return response()->json(Payment::with(['order'])->get());
    }

    /**
     * Store a new payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $payment = Payment::create($request->all());

        return response()->json(['message' => 'Payment created successfully!', 'data' => $payment], 201);
    }

    /**
     * Display a single payment.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load(['order']));
    }

    /**
     * Update a payment.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'order_id' => 'sometimes|exists:orders,id',
        ]);

        $payment->update($request->all());

        return response()->json(['message' => 'Payment updated successfully!', 'data' => $payment]);
    }

    /**
     * Delete a payment.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully!']);
    }
}
