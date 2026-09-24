<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seller dashboard · Trove</title>
    <style>
        :root {
            --ink: #13221D; --ink-light: #1E332B; --paper: #F4F5F1;
            --line: #DBDDD4; --amber: #C98A3C; --amber-deep: #A66D28; --muted: #6B7568;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--paper); color: var(--ink); }
        h1, h2 { font-family: Georgia, 'Times New Roman', serif; margin: 0; font-weight: 500; }

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
        .side-footer .role { margin-top: 2px; font-size: 12px; color: rgba(244,245,241,0.45); }
        .logout-link { margin-top: 14px; background: none; border: none; color: rgba(244,245,241,0.55); font-size: 14px; cursor: pointer; padding: 0; }
        .logout-link:hover { color: var(--paper); }

        header.topbar { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--line); padding: 20px 24px; }
        @media (min-width: 1024px) { header.topbar { padding: 20px 40px; } }
        .date { margin: 0; font-size: 12px; color: var(--muted); }
        .greeting { font-size: 24px; margin-top: 2px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-new { background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 10px 16px; font-size: 14px; font-weight: 500; text-decoration: none; display: none; }
        @media (min-width: 640px) { .btn-new { display: inline-block; } }
        .btn-new:hover { background: var(--ink-light); }
        .logout-mobile { font-size: 14px; color: var(--muted); background: none; border: none; cursor: pointer; }
        @media (min-width: 1024px) { .logout-mobile { display: none; } }

        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (min-width: 640px) { .stats { grid-template-columns: repeat(4, 1fr); } }
        .stat-card { border: 1px solid var(--line); background: #fff; border-radius: 6px; padding: 16px 20px; }
        .status-toast { margin: 24px 24px 0; font-size: 14px; color: var(--amber-deep); background: rgba(201,138,60,0.1); border: 1px solid rgba(201,138,60,0.3); border-radius: 6px; padding: 10px 14px; }
        @media (min-width: 1024px) { .status-toast { margin: 24px 40px 0; } }
        .stat-label { margin: 0; font-size: 12px; color: var(--muted); }
        .stat-value-row { margin-top: 6px; display: flex; align-items: baseline; gap: 8px; }
        .stat-value { margin: 0; font-family: Georgia, serif; font-size: 24px; }
        .stat-delta { font-size: 12px; font-weight: 500; color: #2F6B45; }

        .grid-main { margin-top: 40px; display: grid; grid-template-columns: 1fr; gap: 32px; }
        @media (min-width: 1024px) { .grid-main { grid-template-columns: 2fr 1fr; } }

        .section-head { display: flex; align-items: center; justify-content: space-between; }
        .section-head h2 { font-size: 20px; }
        .section-head a { font-size: 14px; color: var(--muted); text-decoration: none; }
        .section-head a:hover { color: var(--ink); }

        .table-wrap { margin-top: 16px; overflow-x: auto; border: 1px solid var(--line); background: #fff; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; font-size: 12px; color: var(--muted); font-weight: 500; padding: 12px 20px; border-bottom: 1px solid var(--line); }
        td { padding: 14px 20px; border-bottom: 1px solid var(--line); }
        tr:last-child td { border-bottom: none; }
        .muted-cell { color: var(--muted); }
        .col-optional { display: none; }
        @media (min-width: 640px) { .col-optional-sm { display: table-cell; } }

        .pill { font-size: 12px; padding: 3px 10px; border-radius: 999px; font-weight: 500; display: inline-block; }
        .pill-fulfilled { background: rgba(76,124,89,0.12); color: #2F6B45; }
        .pill-pending { background: rgba(201,138,60,0.14); color: #A66D28; }

        .stock-list { margin-top: 16px; border: 1px solid var(--line); background: #fff; border-radius: 6px; }
        .stock-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; font-size: 14px; border-bottom: 1px solid var(--line); }
        .stock-row:last-child { border-bottom: none; }
        .stock-left { color: var(--amber-deep); font-weight: 500; }
        .empty { padding: 16px 20px; font-size: 14px; color: var(--muted); }
    </style>
</head>
<body>
    {{-- $stats, $orders, $lowStock are passed in by LoginController@dashboard --}}

    <div class="layout">
        <aside class="sidebar">
            <div>
                <a href="/" class="logo"><span class="dot"></span>Trove</a>
                <nav>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('dashboard') }}" class="active">Overview</a>
                    <a href="#">Listings</a>
                    <a href="{{ route('seller.orders') }}">Orders</a>
                    <a href="{{ route('seller.payouts') }}">Payouts</a>
                    <a href="#">Settings</a>
                </nav>
            </div>
            <div class="side-footer">
                <p>{{ auth()->user()->store_name ?? auth()->user()->name }}</p>
                <p class="role">Seller account</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Log out</button>
                </form>
            </div>
        </aside>

        <div>
            <header class="topbar">
                <div>
                    <p class="date">{{ now()->format('l, F j') }}</p>
                    <h1 class="greeting">{{ auth()->user()->store_name ?? 'Your store' }}</h1>
                </div>
                <div class="topbar-actions">
                    <a href="{{ route('listings.create') }}" class="btn-new">+ New listing</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-mobile">Log out</button>
                    </form>
                </div>
            </header>

            @if (session('status'))
                <div class="status-toast">{{ session('status') }}</div>
            @endif

            <main>
                <div class="stats">
                    @foreach ($stats as $stat)
                        <div class="stat-card">
                            <p class="stat-label">{{ $stat['label'] }}</p>
                            <div class="stat-value-row">
                                <p class="stat-value">{{ $stat['value'] }}</p>
                                @if ($stat['delta'])
                                    <span class="stat-delta">{{ $stat['delta'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid-main">
                    <div>
                        <div class="section-head">
                            <h2>Recent orders</h2>
                            <a href="{{ route('seller.orders') }}">View all</a>
                        </div>
                        @if ($orders->isEmpty())
                            <p style="margin-top: 16px; font-size: 14px; color: var(--muted);">No orders yet.</p>
                        @else
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Item</th>
                                        <th class="col-optional col-optional-sm">Buyer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="muted-cell">{{ $order['id'] }}</td>
                                            <td>{{ $order['item'] }}</td>
                                            <td class="muted-cell col-optional col-optional-sm">{{ $order['buyer'] }}</td>
                                            <td>{{ $order['total'] }}</td>
                                            <td>
                                                @php
                                                    $cls = $order['status'] === 'fulfilled' ? 'pill-fulfilled' : 'pill-pending';
                                                @endphp
                                                <span class="pill {{ $cls }}" style="text-transform: capitalize;">{{ $order['status'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>

                    <div>
                        <h2>Running low</h2>
                        <div class="stock-list">
                            @forelse ($lowStock as $item)
                                <div class="stock-row">
                                    <span>{{ $item['name'] }}</span>
                                    <span class="stock-left">{{ $item['left'] }} left</span>
                                </div>
                            @empty
                                <p class="empty">Nothing running low right now.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>