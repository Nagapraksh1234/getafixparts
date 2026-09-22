<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your cart · Trove</title>
    <style>
        :root { --ink: #13221D; --ink-light: #1E332B; --paper: #F4F5F1; --line: #DBDDD4; --amber: #C98A3C; --amber-deep: #A66D28; --muted: #6B7568; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--paper); color: var(--ink); }
        h1, h2 { font-family: Georgia, serif; margin: 0; font-weight: 500; }
        a { color: inherit; }

        .layout { display: block; min-height: 100vh; }
        @media (min-width: 1024px) { .layout { display: grid; grid-template-columns: 240px 1fr; } }
        .sidebar { display: none; flex-direction: column; justify-content: space-between; background: var(--ink); color: var(--paper); padding: 32px 24px; }
        @media (min-width: 1024px) { .sidebar { display: flex; } }
        .logo { display: flex; align-items: center; gap: 8px; font-size: 18px; font-weight: 600; text-decoration: none; }
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--amber); }
        nav { margin-top: 40px; display: flex; flex-direction: column; gap: 4px; font-size: 14px; }
        nav a { color: rgba(244,245,241,0.55); text-decoration: none; padding: 8px 12px; border-radius: 6px; }
        nav a.active, nav a:hover { color: var(--paper); background: rgba(244,245,241,0.06); }
        .side-footer { border-top: 1px solid rgba(244,245,241,0.15); padding-top: 16px; }
        .side-footer p { margin: 0; font-size: 14px; font-weight: 500; }
        .side-footer .role { margin-top: 2px; font-size: 12px; color: rgba(244,245,241,0.45); }
        .logout-link { margin-top: 14px; background: none; border: none; color: rgba(244,245,241,0.55); font-size: 14px; cursor: pointer; padding: 0; }

        header.topbar { border-bottom: 1px solid var(--line); padding: 24px; }
        @media (min-width: 1024px) { header.topbar { padding: 24px 40px; } }
        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .cart-layout { display: grid; grid-template-columns: 1fr; gap: 32px; }
        @media (min-width: 900px) { .cart-layout { grid-template-columns: 2fr 1fr; } }

        .cart-row { display: flex; gap: 16px; align-items: center; border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 16px; margin-bottom: 12px; }
        .cart-thumb { width: 72px; height: 72px; border-radius: 6px; background: linear-gradient(135deg, #E7E9E1, #F4F5F1); flex-shrink: 0; overflow: hidden; }
        .cart-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .cart-info { flex: 1; }
        .cart-name { font-weight: 600; font-size: 15px; }
        .cart-seller { font-size: 13px; color: var(--muted); margin-top: 2px; }
        .cart-price { font-family: Georgia, serif; font-size: 15px; margin-top: 6px; }

        .qty-form { display: flex; align-items: center; gap: 8px; }
        .qty-form input { width: 52px; border: 1px solid var(--line); border-radius: 6px; padding: 6px 8px; font-size: 14px; text-align: center; }
        .qty-form button { border: 1px solid var(--line); background: #fff; border-radius: 6px; padding: 6px 10px; font-size: 13px; cursor: pointer; }

        .remove-btn { background: none; border: none; color: var(--muted); font-size: 13px; cursor: pointer; text-decoration: underline; }
        .remove-btn:hover { color: #b42318; }

        .summary { border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 20px; align-self: start; }
        .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px; }
        .summary-total { font-family: Georgia, serif; font-size: 20px; margin-top: 8px; }
        .checkout-btn { width: 100%; margin-top: 16px; background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 12px; font-size: 14px; font-weight: 500; cursor: pointer; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state a { color: var(--amber-deep); font-weight: 500; }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div>
                <a href="/" class="logo"><span class="dot"></span>Trove</a>
                <nav>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('dashboard') }}">Overview</a>
                    <a href="{{ route('wishlist.index') }}">Wishlist</a>
                    <a href="{{ route('cart.index') }}" class="active">Cart</a>
                    <a href="{{ route('orders.index') }}">Orders</a>
                    <a href="#">Settings</a>
                </nav>
            </div>
            <div class="side-footer">
                <p>{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Log out</button>
                </form>
            </div>
        </aside>

        <div>
            <header class="topbar">
                <h1>Your cart</h1>
            </header>

            <main>
                @if ($items->isEmpty())
                    <div class="empty-state">
                        <p>Your cart is empty.</p>
                        <a href="{{ route('home') }}">Browse items on sale</a>
                    </div>
                @else
                    <div class="cart-layout">
                        <div>
                            @foreach ($items as $cartItem)
                                <div class="cart-row">
                                    <div class="cart-thumb">
                                        @if (!empty($cartItem->product->image) && file_exists(public_path($cartItem->product->image)))
                                            <img src="{{ asset($cartItem->product->image) }}" alt="{{ $cartItem->product->name }}">
                                        @endif
                                    </div>
                                    <div class="cart-info">
                                        <p class="cart-name">{{ $cartItem->product->name }}</p>
                                        <p class="cart-seller">{{ $cartItem->product->seller->store_name ?? $cartItem->product->seller->name }}</p>
                                        <p class="cart-price">${{ number_format($cartItem->product->price, 2) }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.update', $cartItem) }}" class="qty-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $cartItem->quantity }}" min="1" max="99">
                                        <button type="submit">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $cartItem) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-btn">Remove</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary">
                            <div class="summary-row"><span>Items</span><span>{{ $items->sum('quantity') }}</span></div>
                            <div class="summary-row summary-total"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                            <a href="{{ route('checkout.show') }}" class="checkout-btn" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Checkout</a>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>