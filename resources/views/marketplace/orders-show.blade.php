<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ $order->id }} · Trove</title>
    <style>
        :root { --ink: #13221D; --ink-light: #1E332B; --paper: #F4F5F1; --line: #DBDDD4; --amber: #C98A3C; --amber-deep: #A66D28; --muted: #6B7568; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--paper); color: var(--ink); }
        h1, h2 { font-family: Georgia, serif; margin: 0; font-weight: 500; }

        .layout { display: block; min-height: 100vh; }
        @media (min-width: 1024px) { .layout { display: grid; grid-template-columns: 240px 1fr; } }
        .sidebar { display: none; flex-direction: column; justify-content: space-between; background: var(--ink); color: var(--paper); padding: 32px 24px; }
        @media (min-width: 1024px) { .sidebar { display: flex; } }
        .logo { display: flex; align-items: center; gap: 8px; font-size: 18px; font-weight: 600; text-decoration: none; color: inherit; }
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--amber); }
        nav { margin-top: 40px; display: flex; flex-direction: column; gap: 4px; font-size: 14px; }
        nav a { color: rgba(244,245,241,0.55); text-decoration: none; padding: 8px 12px; border-radius: 6px; }
        nav a.active, nav a:hover { color: var(--paper); background: rgba(244,245,241,0.06); }
        .side-footer { border-top: 1px solid rgba(244,245,241,0.15); padding-top: 16px; }
        .side-footer p { margin: 0; font-size: 14px; font-weight: 500; }
        .logout-link { margin-top: 14px; background: none; border: none; color: rgba(244,245,241,0.55); font-size: 14px; cursor: pointer; padding: 0; }

        header.topbar { border-bottom: 1px solid var(--line); padding: 24px; }
        @media (min-width: 1024px) { header.topbar { padding: 24px 40px; } }
        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .confirm-banner { max-width: 720px; background: rgba(76,124,89,0.1); border: 1px solid rgba(76,124,89,0.3); color: #2F6B45; border-radius: 8px; padding: 16px 20px; font-size: 14px; margin-bottom: 24px; }

        .card { max-width: 720px; background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 24px; margin-bottom: 20px; }
        .card h2 { font-size: 16px; margin-bottom: 14px; }
        .meta-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: var(--muted); }
        .meta-row strong { color: var(--ink); font-weight: 500; }

        .item-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: 14px; }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-weight: 500; }
        .item-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .total-row { display: flex; justify-content: space-between; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--line); font-family: Georgia, serif; font-size: 18px; }

        .link-row { max-width: 720px; }
        .btn-link { display: inline-block; background: var(--ink); color: var(--paper); text-decoration: none; border-radius: 6px; padding: 11px 20px; font-size: 14px; font-weight: 500; }
        .btn-link:hover { background: var(--ink-light); }
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
                    <a href="{{ route('cart.index') }}">Cart</a>
                    <a href="{{ route('orders.index') }}" class="active">Orders</a>
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
                <h1>Order #{{ $order->id }}</h1>
            </header>

            <main>
                @if (session('status'))
                    <div class="confirm-banner">{{ session('status') }} We've sent the details to your account — track it any time from Orders.</div>
                @endif

                <div class="card">
                    <h2>Shipping to</h2>
                    <div class="meta-row"><span>Name</span><strong>{{ $order->shipping_name }}</strong></div>
                    <div class="meta-row"><span>Phone</span><strong>{{ $order->shipping_phone }}</strong></div>
                    <div class="meta-row"><span>Address</span><strong>{{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_postal_code }}</strong></div>
                    <div class="meta-row"><span>Status</span><strong style="text-transform: capitalize;">{{ $order->status }}</strong></div>
                    <div class="meta-row"><span>Placed</span><strong>{{ $order->created_at->format('M j, Y \a\t g:i A') }}</strong></div>
                </div>

                <div class="card">
                    <h2>Items</h2>
                    @foreach ($order->items as $orderItem)
                        <div class="item-row">
                            <div>
                                <p class="item-name">{{ $orderItem->product->name }} &times; {{ $orderItem->quantity }}</p>
                                <p class="item-meta">{{ $orderItem->product->seller->store_name ?? $orderItem->product->seller->name }}</p>
                            </div>
                            <span>${{ number_format($orderItem->lineTotal(), 2) }}</span>
                        </div>
                    @endforeach
                    <div class="total-row">
                        <span>Total</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                </div>

                <div class="link-row">
                    <a href="{{ route('home') }}" class="btn-link">Continue shopping</a>
                </div>
            </main>
        </div>
    </div>
</body>
</html>