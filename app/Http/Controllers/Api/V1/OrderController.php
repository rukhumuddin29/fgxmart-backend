<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\UserAddress;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use App\Models\OrderStatusLog;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $address = UserAddress::with('country')->findOrFail($request->address_id);
        $primaryBank = BankAccount::where('is_primary', true)->first();

        // Calculate totals on server-side
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->discount_price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
        }

        $shipping = 0; // Future logic here
        $tax = 0;      // Future logic here
        $total = $subtotal + $shipping + $tax;

        return DB::transaction(function () use ($user, $cartItems, $address, $primaryBank, $subtotal, $shipping, $tax, $total, $request) {
            $order = Order::create([
                'order_number' => 'FGX-' . strtoupper(Str::random(8)),
                'view_hash' => Str::random(40),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'shipping_charges' => $shipping,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'shipping_address' => $address->toArray(),
                'bank_details' => $primaryBank ? $primaryBank->toArray() : null,
                'notes' => $request->notes,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($cartItems as $item) {
                $price = $item->product->discount_price ?? $item->product->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'subtotal' => $price * $item->quantity,
                ]);
            }

            // Clear Cart
            CartItem::where('user_id', $user->id)->delete();

            // Send Email (Async or Try-Catch recommended)
            try {
                Mail::to($user->email)->send(new OrderPlacedMail($order->load('items')));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Mail failed for order {$order->order_number}: " . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => $order
            ]);
        });
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function show(Request $request, $orderNumber)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->with(['items.product', 'user'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    /**
     * Publicly view an order using a secure hash (No login required)
     */
    public function publicShow(Request $request, $orderNumber)
    {
        $hash = $request->query('hash');
        
        if (!$hash) {
            return response()->json(['message' => 'Secure hash is required'], 403);
        }

        $order = Order::where('order_number', $orderNumber)
            ->where('view_hash', $hash)
            ->with('items.product')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    /**
     * Admin view of all orders
     */
    public function adminIndex(Request $request)
    {
        $orders = Order::with(['items', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Admin view of a single order with logs
     */
    public function adminShow(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'user', 'statusLogs.user'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    /**
     * Admin update order status
     */
    public function updateStatus(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        $request->validate([
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($order, $request) {
            $user = $request->user();

            // Handle Order Status Change
            if ($request->has('status') && $request->status !== $order->status) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'type' => 'order_status',
                    'old_status' => $order->status,
                    'new_status' => $request->status,
                    'notes' => $request->notes,
                ]);
                $order->status = $request->status;
            }

            // Handle Payment Status Change
            if ($request->has('payment_status') && $request->payment_status !== $order->payment_status) {
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'type' => 'payment_status',
                    'old_status' => $order->payment_status,
                    'new_status' => $request->payment_status,
                    'notes' => $request->notes,
                ]);
                $order->payment_status = $request->payment_status;
            }

            $order->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Order updated successfully',
            'data' => $order->load(['statusLogs.user'])
        ]);
    }
}
