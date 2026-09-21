<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your wishlist · Trove</title>
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

        .grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media (min-width: 560px) { .grid { grid-template-columns: 1fr 1fr; } }
        @media (min-width: 860px) { .grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1200px) { .grid { grid-template-columns: repeat(4, 1fr); } }

        .item-card { border: 1px solid var(--line); background: #fff; border-radius: 8px; overflow: hidden; }
        .item-thumb { aspect-ratio: 4/3; background: linear-gradient(135deg, #E7E9E1, #F4F5F1); display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 12px; overflow: hidden; }
        .item-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .item-body { padding: 14px 16px; }
        .item-category { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.04em; }
        .item-name { margin-top: 4px; font-size: 15px; font-weight: 600; }
        .item-seller { margin-top: 2px; font-size: 13px; color: var(--muted); }
        .item-price { margin-top: 10px; font-family: Georgia, serif; font-size: 18px; }

        .item-actions { display: flex; gap: 8px; margin-top: 12px; }
        .btn-add-cart { flex: 1; background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 9px 12px; font-size: 13px; font-weight: 500; cursor: pointer; }
        .btn-add-cart:hover { background: var(--ink-light); }
        .remove-btn { border: 1px solid var(--line); background: #fff; border-radius: 6px; padding: 9px 12px; font-size: 13px; cursor: pointer; color: var(--muted); }
        .remove-btn:hover { border-color: #b42318; color: #b42318; }

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
                    <a href="{{ route('wishlist.index') }}" class="active">Wishlist</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
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
                <h1>Your wishlist</h1>
            </header>

            <main>
                @if ($items->isEmpty())
                    <div class="empty-state">
                        <p>Nothing saved yet.</p>
                        <a href="{{ route('home') }}">Browse items on sale</a>
                    </div>
                @else
                    <div class="grid">
                        @foreach ($items as $wishlistItem)
                            @php $product = $wishlistItem->product; @endphp
                            <div class="item-card">
                                <div class="item-thumb">
                                    @if (!empty($product->image) && file_exists(public_path($product->image)))
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        No image
                                    @endif
                                </div>
                                <div class="item-body">
                                    <p class="item-category">{{ $product->category }}</p>
                                    <p class="item-name">{{ $product->name }}</p>
                                    <p class="item-seller">{{ $product->seller->store_name ?? $product->seller->name }}</p>
                                    <p class="item-price">${{ number_format($product->price, 2) }}</p>

                                    <div class="item-actions">
                                        <form method="POST" action="{{ route('cart.store', $product) }}" style="flex: 1;">
                                            @csrf
                                            <button type="submit" class="btn-add-cart">Add to cart</button>
                                        </form>
                                        <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                                            @csrf
                                            <button type="submit" class="remove-btn">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>