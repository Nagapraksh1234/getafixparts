<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payouts · Trove</title>
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

        .summary-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 560px; }
        .summary-card { border: 1px solid var(--line); background: #fff; border-radius: 8px; padding: 20px; }
        .summary-label { font-size: 13px; color: var(--muted); }
        .summary-value { margin-top: 6px; font-family: Georgia, serif; font-size: 28px; }
        .summary-note { margin-top: 4px; font-size: 12px; color: var(--muted); }

        .section { margin-top: 36px; }
        .section h2 { font-size: 18px; margin-bottom: 14px; }

        .table-wrap { overflow-x: auto; border: 1px solid var(--line); background: #fff; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; font-size: 12px; color: var(--muted); font-weight: 500; padding: 12px 20px; border-bottom: 1px solid var(--line); }
        td { padding: 14px 20px; border-bottom: 1px solid var(--line); }
        tr:last-child td { border-bottom: none; }
        .muted-cell { color: var(--muted); }

        .empty-state { padding: 24px 20px; color: var(--muted); font-size: 14px; }
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
                    <a href="{{ route('seller.orders') }}">Orders</a>
                    <a href="{{ route('seller.payouts') }}" class="active">Payouts</a>
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
                <h1>Payouts</h1>
            </header>

            <main>
                <div class="summary-row">
                    <div class="summary-card">
                        <p class="summary-label">Total earned</p>
                        <p class="summary-value">${{ number_format($totalEarned, 2) }}</p>
                        <p class="summary-note">From fulfilled orders</p>
                    </div>
                    <div class="summary-card">
                        <p class="summary-label">Pending payout</p>
                        <p class="summary-value">${{ number_format($pendingPayout, 2) }}</p>
                        <p class="summary-note">Releases once orders are fulfilled</p>
                    </div>
                </div>

                <div class="section">
                    <h2>Payout history</h2>
                    <div class="table-wrap">
                        @if ($fulfilledItems->isEmpty())
                            <p class="empty-state">Nothing paid out yet — this fills in once you mark orders fulfilled.</p>
                        @else
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Item</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fulfilledItems as $item)
                                        <tr>
                                            <td class="muted-cell">#TR-{{ str_pad($item->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td class="muted-cell">{{ $item->updated_at->format('M j, Y') }}</td>
                                            <td>${{ number_format($item->lineTotal(), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>