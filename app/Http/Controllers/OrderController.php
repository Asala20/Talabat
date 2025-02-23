<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display all orders for the authenticated user.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->with('orderItems.product')->get();
        return response()->json($orders);
    }

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $request->total_price,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['quantity'] * 100, // Example pricing logic
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Order placed successfully!', 'data' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order failed!', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a single order.
     */
    public function show(Order $order)
    {
        return response()->json($order->load(['orderItems.product', 'payment']));
    }

    /**
     * Delete an order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully!']);
    }
}
