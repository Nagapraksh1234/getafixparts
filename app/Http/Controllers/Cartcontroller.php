<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = CartItem::with('product.seller')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $subtotal = $items->sum(fn (CartItem $item) => $item->lineTotal());

        return view('marketplace.cart', [
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Product $product): RedirectResponse
    {
        $item = CartItem::firstOrNew([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        $item->quantity = $item->exists ? $item->quantity + 1 : 1;
        $item->save();

        return back()->with('status', "Added \"{$product->name}\" to your cart.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back();
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $cartItem->delete();

        return back();
    }
}