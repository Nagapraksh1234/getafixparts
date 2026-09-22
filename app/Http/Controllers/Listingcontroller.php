<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function create(): View
    {
        abort_unless(auth()->user()->role === 'seller', 403, 'Only sellers can add listings.');

        return view('seller.create-listing');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'seller', 403, 'Only sellers can add listings.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'gt:price'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // 4MB
        ], [
            'original_price.gt' => 'The original price must be higher than the sale price.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid('product_') . '.' . $file->getClientOriginalExtension();

            // Saves straight into public/images/products so it works with
            // the same asset()/public_path() checks used on the Home page —
            // no storage:link needed.
            $file->move(public_path('images/products'), $filename);

            $imagePath = 'images/products/' . $filename;
        }

        Product::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('status', 'Listing published.');
    }
}