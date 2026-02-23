<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array',
        ]);

        $cartItem = CartItem::where([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'options' => $request->options,
        ])->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'options' => $request->options,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json($cartItem->load('product'));
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json($cartItem->load('product'));
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed']);
    }

    /**
     * Sync guest cart items to the user's cart upon login.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.options' => 'nullable|array',
        ]);

        $userId = $request->user()->id;

        foreach ($request->items as $item) {
            $cartItem = CartItem::where([
                'user_id' => $userId,
                'product_id' => $item['product_id'],
                'options' => $item['options'] ?? null,
            ])->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $item['quantity']);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'options' => $item['options'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        return response()->json(['message' => 'Cart synced successfully']);
    }
}
