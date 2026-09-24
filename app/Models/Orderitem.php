<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function lineTotal(): float
    {
        return (float) $this->price * $this->quantity;
    }

    /**
     * Mark this line fulfilled, then promote the parent Order to
     * "fulfilled" too if every line on it (across all sellers) is done.
     */
    public function markFulfilled(): void
    {
        $this->update(['status' => 'fulfilled']);

        $order = $this->order;
        $allDone = $order->items()->where('status', '!=', 'fulfilled')->doesntExist();

        if ($allDone) {
            $order->update(['status' => 'fulfilled']);
        }
    }
}