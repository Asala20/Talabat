<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Display all order items.
     */
    public function index()
    {
        return response()->json(OrderItem::with(['order', 'product'])->get());
    }

    /**
     * Store a new order item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:0',
        ]);

        $orderItem = OrderItem::create($request->all());

        return response()->json(['message' => 'Order item created successfully!', 'data' => $orderItem], 201);
    }

    /**
     * Display a single order item.
     */
    public function show(OrderItem $orderItem)
    {
        return response()->json($orderItem->load(['order', 'product']));
    }

    /**
     * Update an order item.
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|integer|min:0',
        ]);

        $orderItem->update($request->all());

        return response()->json(['message' => 'Order item updated successfully!', 'data' => $orderItem]);
    }

    /**
     * Delete an order item.
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return response()->json(['message' => 'Order item deleted successfully!']);
    }
}
