<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WishlistItem;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = WishlistItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($items);
    }

    /**
     * Toggle a product in/out of the wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        $exists = WishlistItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['message' => 'Removed from wishlist', 'attached' => false]);
        }

        WishlistItem::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json(['message' => 'Added to wishlist', 'attached' => true]);
    }

    /**
     * Sync guest wishlist items to the user's wishlist upon login.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id;

        foreach ($request->product_ids as $productId) {
            WishlistItem::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        return response()->json(['message' => 'Wishlist synced successfully']);
    }
}
