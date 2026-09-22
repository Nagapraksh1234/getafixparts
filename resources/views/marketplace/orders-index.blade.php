<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your orders · Trove</title>
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

        .order-card { display: block; text-decoration: none; color: inherit; background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 18px 20px; margin-bottom: 12px; }
        .order-card:hover { border-color: var(--amber); }
        .order-top { display: flex; align-items: center; justify-content: space-between; }
        .order-id { font-weight: 600; font-size: 15px; }
        .order-date { font-size: 13px; color: var(--muted); margin-top: 2px; }
        .order-total { font-family: Georgia, serif; font-size: 18px; }
        .pill { font-size: 12px; padding: 3px 10px; border-radius: 999px; font-weight: 500; display: inline-block; margin-top: 8px; text-transform: capitalize; }
        .pill-pending { background: rgba(201,138,60,0.14); color: #A66D28; }
        .pill-fulfilled { background: rgba(76,124,89,0.12); color: #2F6B45; }
        .pill-cancelled { background: rgba(107,117,104,0.12); color: #6B7568; }

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
                <h1>Your orders</h1>
            </header>

            <main>
                @if ($orders->isEmpty())
                    <div class="empty-state">
                        <p>You haven't placed any orders yet.</p>
                        <a href="{{ route('home') }}">Browse items on sale</a>
                    </div>
                @else
                    @foreach ($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="order-card">
                            <div class="order-top">
                                <div>
                                    <p class="order-id">Order #{{ $order->id }}</p>
                                    <p class="order-date">{{ $order->created_at->format('M j, Y') }} &middot; {{ $order->itemCount() }} {{ Str::plural('item', $order->itemCount()) }}</p>
                                </div>
                                <span class="order-total">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @php
                                $pillClass = match ($order->status) {
                                    'fulfilled' => 'pill-fulfilled',
                                    'cancelled' => 'pill-cancelled',
                                    default => 'pill-pending',
                                };
                            @endphp
                            <span class="pill {{ $pillClass }}">{{ $order->status }}</span>
                        </a>
                    @endforeach
                @endif
            </main>
        </div>
    </div>
</body>
</html>