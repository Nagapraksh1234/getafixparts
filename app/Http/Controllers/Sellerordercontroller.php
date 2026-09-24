<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->role === 'seller', 403);

        $orderItems = OrderItem::with(['order', 'product'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->get();

        return view('seller.orders', ['orderItems' => $orderItems]);
    }

    public function fulfill(OrderItem $orderItem): RedirectResponse
    {
        abort_unless($orderItem->seller_id === auth()->id(), 403);

        $orderItem->markFulfilled();

        return back()->with('status', 'Marked as fulfilled.');
    }
}