<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home · Trove</title>
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

        header.topbar { border-bottom: 1px solid var(--line); padding: 24px; }
        @media (min-width: 1024px) { header.topbar { padding: 24px 40px; } }
        .greeting { font-size: 24px; }
        .lead { margin-top: 6px; color: var(--muted); font-size: 14px; }

        .search-row { margin-top: 20px; display: flex; gap: 10px; }
        .search-box { flex: 1; position: relative; }
        .search-box input {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 6px;
            padding: 11px 14px 11px 38px;
            font-size: 14px;
            font-family: inherit;
            color: var(--ink);
        }
        .search-box input:focus { outline: none; border-color: var(--amber-deep); }
        .search-box svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--muted); }
        .search-btn { background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 0 20px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .search-btn:hover { background: var(--ink-light); }
        .clear-link { align-self: center; font-size: 14px; color: var(--muted); text-decoration: none; }
        .clear-link:hover { color: var(--ink); }

        .chips { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; }
        .chip { border: 1px solid var(--line); background: #fff; border-radius: 999px; padding: 7px 14px; font-size: 13px; text-decoration: none; color: var(--ink); }
        .chip:hover { border-color: var(--amber); color: var(--amber-deep); }

        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .result-count { font-size: 14px; color: var(--muted); margin-bottom: 16px; }

        .grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media (min-width: 560px) { .grid { grid-template-columns: 1fr 1fr; } }
        @media (min-width: 860px) { .grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1200px) { .grid { grid-template-columns: repeat(4, 1fr); } }

        .item-card { border: 1px solid var(--line); background: #fff; border-radius: 8px; overflow: hidden; }
        .item-thumb {
            aspect-ratio: 4 / 3;
            background: linear-gradient(135deg, #E7E9E1, #F4F5F1);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); font-size: 12px;
            position: relative;
            overflow: hidden;
        }
        .item-thumb img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }
        .sale-badge {
            position: absolute; top: 10px; left: 10px;
            background: var(--amber); color: #fff;
            font-size: 11px; font-weight: 600;
            padding: 3px 8px; border-radius: 999px;
        }
        .item-body { padding: 14px 16px; }
        .item-category { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .item-name { margin-top: 4px; font-size: 15px; font-weight: 600; }
        .item-seller { margin-top: 2px; font-size: 13px; color: var(--muted); }
        .item-price-row { margin-top: 10px; display: flex; align-items: baseline; gap: 8px; }
        .item-price { font-family: Georgia, serif; font-size: 18px; }
        .item-original { font-size: 13px; color: var(--muted); text-decoration: line-through; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
    </style>
</head>
<body>
    @php $isSeller = auth()->user()->role === 'seller'; @endphp

    <div class="layout">
        <aside class="sidebar">
            <div>
                <a href="/" class="logo"><span class="dot"></span>Trove</a>
                <nav>
                    <a href="{{ route('home') }}" class="active">Home</a>
                    <a href="{{ route('dashboard') }}">Overview</a>
                    @if ($isSeller)
                        <a href="#">Listings</a>
                        <a href="#">Orders</a>
                        <a href="#">Payouts</a>
                    @else
                        <a href="#">Orders</a>
                        <a href="#">Saved items</a>
                        <a href="#">Messages</a>
                    @endif
                    <a href="#">Settings</a>
                </nav>
            </div>
            <div class="side-footer">
                <p>{{ $isSeller ? (auth()->user()->store_name ?? auth()->user()->name) : auth()->user()->name }}</p>
                <p class="role">{{ $isSeller ? 'Seller account' : 'Buyer account' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Log out</button>
                </form>
            </div>
        </aside>

        <div>
            <header class="topbar">
                <h1 class="greeting">Browse what's on sale</h1>
                <p class="lead">Search across every seller's storefront on Trove.</p>

                <form method="GET" action="{{ route('home') }}" class="search-row">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="q" value="{{ $query }}" placeholder="Search items, categories, or sellers">
                    </div>
                    <button type="submit" class="search-btn">Search</button>
                    @if ($query !== '')
                        <a href="{{ route('home') }}" class="clear-link">Clear</a>
                    @endif
                </form>

                <div class="chips">
                    @foreach ($categories as $category)
                        <a href="{{ route('home', $category === 'All' ? [] : ['q' => $category]) }}" class="chip">{{ $category }}</a>
                    @endforeach
                </div>
            </header>

            <main>
                <p class="result-count">
                    @if ($query !== '')
                        {{ count($items) }} {{ Str::plural('result', count($items)) }} for "{{ $query }}"
                    @else
                        {{ count($items) }} items on sale right now
                    @endif
                </p>

                @if (count($items) > 0)
                    <div class="grid">
                        @foreach ($items as $item)
                            <div class="item-card">
                                <div class="item-thumb">
                                    @if ($item->isOnSale())
                                        <span class="sale-badge">Sale</span>
                                    @endif
                                    @if (!empty($item->image) && file_exists(public_path($item->image)))
                                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        No image
                                    @endif
                                </div>
                                <div class="item-body">
                                    <p class="item-category">{{ $item->category }}</p>
                                    <p class="item-name">{{ $item->name }}</p>
                                    <p class="item-seller">{{ $item->seller->store_name ?? $item->seller->name }}</p>
                                    <div class="item-price-row">
                                        <span class="item-price">${{ number_format($item->price, 2) }}</span>
                                        @if ($item->isOnSale())
                                            <span class="item-original">${{ number_format($item->original_price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <p>No items match "{{ $query }}". Try a different search.</p>
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>