<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->role === 'seller', 403);

        $orderItems = OrderItem::with(['order', 'product'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->get();

        $fulfilled = $orderItems->where('status', 'fulfilled');
        $pending = $orderItems->where('status', 'pending');

        return view('seller.payouts', [
            'totalEarned' => $fulfilled->sum(fn (OrderItem $i) => $i->lineTotal()),
            'pendingPayout' => $pending->sum(fn (OrderItem $i) => $i->lineTotal()),
            'fulfilledItems' => $fulfilled->values(),
        ]);
    }
}