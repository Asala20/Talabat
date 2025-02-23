<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display all cart items for the authenticated user.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
        return response()->json($cartItems);
    }

    /**
     * Store a new cart item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return response()->json(['message' => 'Cart updated successfully!', 'data' => $cartItem], 201);
    }

    /**
     * Display a single cart item.
     */
    public function show(Cart $cart)
    {
        return response()->json($cart->load(['product']));
    }

    /**
     * Update a cart item.
     */
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $cart->update($request->only('quantity'));

        return response()->json(['message' => 'Cart item updated successfully!', 'data' => $cart]);
    }

    /**
     * Delete a cart item.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Cart item removed successfully!']);
    }
}
