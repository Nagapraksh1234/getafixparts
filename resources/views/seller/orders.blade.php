<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders · Trove</title>
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

        .status-toast { margin-bottom: 20px; font-size: 14px; color: var(--amber-deep); background: rgba(201,138,60,0.1); border: 1px solid rgba(201,138,60,0.3); border-radius: 6px; padding: 10px 14px; }

        .table-wrap { overflow-x: auto; border: 1px solid var(--line); background: #fff; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; font-size: 12px; color: var(--muted); font-weight: 500; padding: 12px 20px; border-bottom: 1px solid var(--line); }
        td { padding: 14px 20px; border-bottom: 1px solid var(--line); }
        tr:last-child td { border-bottom: none; }
        .muted-cell { color: var(--muted); }

        .pill { font-size: 12px; padding: 3px 10px; border-radius: 999px; font-weight: 500; display: inline-block; text-transform: capitalize; }
        .pill-fulfilled { background: rgba(76,124,89,0.12); color: #2F6B45; }
        .pill-pending { background: rgba(201,138,60,0.14); color: #A66D28; }

        .fulfill-btn { background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 7px 14px; font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .fulfill-btn:hover { background: var(--ink-light); }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
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
                    <a href="{{ route('listings.create') }}">New listing</a>
                    <a href="{{ route('seller.orders') }}" class="active">Orders</a>
                    <a href="{{ route('seller.payouts') }}">Payouts</a>
                </nav>
            </div>
            <div class="side-footer">
                <p>{{ auth()->user()->store_name ?? auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Log out</button>
                </form>
            </div>
        </aside>

        <div>
            <header class="topbar">
                <h1>Orders</h1>
            </header>

            <main>
                @if (session('status'))
                    <div class="status-toast">{{ session('status') }}</div>
                @endif

                @if ($orderItems->isEmpty())
                    <div class="empty-state">
                        <p>No orders yet.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Item</th>
                                    <th>Buyer</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $item)
                                    <tr>
                                        <td class="muted-cell">#TR-{{ str_pad($item->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="muted-cell">{{ $item->order->shipping_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->lineTotal(), 2) }}</td>
                                        <td>
                                            <span class="pill {{ $item->status === 'fulfilled' ? 'pill-fulfilled' : 'pill-pending' }}">{{ $item->status }}</span>
                                        </td>
                                        <td>
                                            @if ($item->status !== 'fulfilled')
                                                <form method="POST" action="{{ route('seller.orders.fulfill', $item) }}">
                                                    @csrf
                                                    <button type="submit" class="fulfill-btn">Mark fulfilled</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>