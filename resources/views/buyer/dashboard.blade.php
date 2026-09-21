<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard · Trove</title>
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
        .logout-mobile { font-size: 14px; color: var(--muted); background: none; border: none; cursor: pointer; }
        @media (min-width: 1024px) { .logout-mobile { display: none; } }

        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (min-width: 640px) { .stats { grid-template-columns: repeat(4, 1fr); } }
        .stat-card { border: 1px solid var(--line); background: #fff; border-radius: 6px; padding: 16px 20px; }
        .stat-label { margin: 0; font-size: 12px; color: var(--muted); }
        .stat-value { margin: 6px 0 0; font-family: Georgia, serif; font-size: 24px; }

        .section { margin-top: 40px; }
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
        @media (min-width: 768px) { .col-optional-md { display: table-cell; } }

        .pill { font-size: 12px; padding: 3px 10px; border-radius: 999px; font-weight: 500; display: inline-block; }
        .pill-delivered { background: rgba(76,124,89,0.12); color: #2F6B45; }
        .pill-transit { background: rgba(201,138,60,0.14); color: #A66D28; }
        .pill-processing { background: rgba(107,117,104,0.12); color: #6B7568; }

        .chips { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { border: 1px solid var(--line); background: #fff; border-radius: 999px; padding: 8px 16px; font-size: 14px; text-decoration: none; color: var(--ink); }
        .chip:hover { border-color: var(--amber); color: var(--amber-deep); }
    </style>
</head>
<body>
    {{-- $stats, $orders, $categories are passed in by LoginController@dashboard --}}

    <div class="layout">
        <aside class="sidebar">
            <div>
                <a href="/" class="logo"><span class="dot"></span>Trove</a>
                <nav>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('dashboard') }}" class="active">Overview</a>
                    <a href="#">Orders</a>
                    <a href="#">Saved items</a>
                    <a href="#">Messages</a>
                    <a href="#">Settings</a>
                </nav>
            </div>
            <div class="side-footer">
                <p>{{ auth()->user()->name }}</p>
                <p class="role">Buyer account</p>
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
                    <h1 class="greeting">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}</h1>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-mobile">Log out</button>
                </form>
            </header>

            <main>
                <div class="stats">
                    @foreach ($stats as $stat)
                        <div class="stat-card">
                            <p class="stat-label">{{ $stat['label'] }}</p>
                            <p class="stat-value">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="section">
                    <div class="section-head">
                        <h2>Recent orders</h2>
                        <a href="#">View all</a>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Item</th>
                                    <th class="col-optional col-optional-sm">Seller</th>
                                    <th class="col-optional col-optional-md">Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="muted-cell">{{ $order['id'] }}</td>
                                        <td>{{ $order['item'] }}</td>
                                        <td class="muted-cell col-optional col-optional-sm">{{ $order['seller'] }}</td>
                                        <td class="muted-cell col-optional col-optional-md">{{ $order['date'] }}</td>
                                        <td>{{ $order['total'] }}</td>
                                        <td>
                                            @php
                                                $cls = match ($order['status']) {
                                                    'Delivered' => 'pill-delivered',
                                                    'In transit' => 'pill-transit',
                                                    default => 'pill-processing',
                                                };
                                            @endphp
                                            <span class="pill {{ $cls }}">{{ $order['status'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section">
                    <h2>Browse by category</h2>
                    <div class="chips">
                        @foreach ($categories as $category)
                            <a href="#" class="chip">{{ $category }}</a>
                        @endforeach
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>