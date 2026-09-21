<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $items = Product::with('seller')
            ->search($query)
            ->latest()
            ->get();

        $wishlistedIds = auth()->user()->wishlistItems()->pluck('product_id')->toArray();
        $cartCount = (int) auth()->user()->cartItems()->sum('quantity');

        return view('marketplace.home', [
            'items' => $items,
            'query' => $query,
            'categories' => ['All', 'Home & Living', 'Ceramics', 'Lighting', 'Textiles', 'Stationery'],
            'wishlistedIds' => $wishlistedIds,
            'cartCount' => $cartCount,
            'wishlistCount' => count($wishlistedIds),
        ]);
    }
}