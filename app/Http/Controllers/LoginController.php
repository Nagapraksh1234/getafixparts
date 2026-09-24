<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'intent' => ['nullable', 'in:buyer,seller'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect('/login');
        }

        return $user->role === 'seller'
            ? view('seller.dashboard', $this->sellerData($user))
            : view('buyer.dashboard', $this->buyerData($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Real data for the buyer dashboard, pulled from orders/wishlist tables.
     */
    protected function buyerData(User $user): array
    {
        $orders = Order::with('items.product.seller')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeOrders = $orders->where('status', 'pending')->count();
        $totalSpent = $orders->sum('subtotal');
        $savedItems = $user->wishlistItems()->count();

        $recentOrders = $orders->take(4)->map(function (Order $order) {
            $firstItem = $order->items->first();
            $extra = $order->items->count() - 1;

            return [
                'id' => '#TR-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'item' => $firstItem
                    ? $firstItem->product->name . ($extra > 0 ? " +{$extra} more" : '')
                    : 'Order',
                'seller' => $firstItem
                    ? ($firstItem->product->seller->store_name ?? $firstItem->product->seller->name)
                    : '—',
                'date' => $order->created_at->format('M j'),
                'total' => '$' . number_format($order->subtotal, 2),
                'status' => $order->status,
            ];
        });

        $categories = Product::query()->distinct()->pluck('category')->filter()->values()->all();

        return [
            'stats' => [
                ['label' => 'Active orders', 'value' => (string) $activeOrders],
                ['label' => 'Total spent', 'value' => '$' . number_format($totalSpent, 2)],
                ['label' => 'Saved items', 'value' => (string) $savedItems],
                ['label' => 'Open messages', 'value' => '—'],
            ],
            'orders' => $recentOrders,
            'categories' => $categories,
        ];
    }

    /**
     * Real data for the seller dashboard, pulled from order_items/products.
     */
    protected function sellerData(User $user): array
    {
        $orderItems = OrderItem::with(['order', 'product'])
            ->where('seller_id', $user->id)
            ->get();

        $revenueThisMonth = $orderItems
            ->filter(fn (OrderItem $item) => $item->order->created_at->isCurrentMonth())
            ->sum(fn (OrderItem $item) => $item->lineTotal());

        $ordersToFulfill = $orderItems->where('status', 'pending')->pluck('order_id')->unique()->count();
        $activeListings = Product::where('user_id', $user->id)->count();

        $recentOrders = $orderItems->sortByDesc('created_at')->take(4)->map(function (OrderItem $item) {
            return [
                'id' => '#TR-' . str_pad($item->order_id, 5, '0', STR_PAD_LEFT),
                'item' => $item->product->name,
                'buyer' => $item->order->shipping_name,
                'date' => $item->created_at->format('M j'),
                'total' => '$' . number_format($item->lineTotal(), 2),
                'status' => $item->status,
                'order_item_id' => $item->id,
            ];
        })->values();

        $lowStock = Product::where('user_id', $user->id)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->get()
            ->map(fn (Product $product) => ['name' => $product->name, 'left' => $product->stock]);

        return [
            'stats' => [
                ['label' => 'Revenue this month', 'value' => '$' . number_format($revenueThisMonth, 2), 'delta' => null],
                ['label' => 'Orders to fulfill', 'value' => (string) $ordersToFulfill, 'delta' => null],
                ['label' => 'Active listings', 'value' => (string) $activeListings, 'delta' => null],
                ['label' => 'Store views', 'value' => '—', 'delta' => null],
            ],
            'orders' => $recentOrders,
            'lowStock' => $lowStock,
        ];
    }
}