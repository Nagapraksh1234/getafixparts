<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Show the shipping form + order summary before placing the order.
     */
    public function checkout(): View|RedirectResponse
    {
        $cartItems = CartItem::with('product.seller')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(fn (CartItem $item) => $item->lineTotal());

        return view('marketplace.checkout', [
            'items' => $cartItems,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Turn the current cart into a real Order + OrderItems, then empty the cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(fn (CartItem $item) => $item->lineTotal());

        $order = DB::transaction(function () use ($validated, $cartItems, $subtotal) {
            $order = Order::create($validated + [
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'seller_id' => $cartItem->product->user_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            CartItem::where('user_id', auth()->id())->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('status', 'Order placed successfully.');
    }

    /**
     * Buyer's order history.
     */
    public function index(): View
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('marketplace.orders-index', ['orders' => $orders]);
    }

    /**
     * A single order's detail / confirmation page.
     */
    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.product.seller');

        return view('marketplace.orders-show', ['order' => $order]);
    }
}