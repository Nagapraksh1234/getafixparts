<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = WishlistItem::with('product.seller')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('marketplace.wishlist', ['items' => $items]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        $existing = WishlistItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = "Removed \"{$product->name}\" from your wishlist.";
        } else {
            WishlistItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $message = "Added \"{$product->name}\" to your wishlist.";
        }

        return back()->with('status', $message);
    }
}