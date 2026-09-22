<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout · Trove</title>
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

        .checkout-layout { display: grid; grid-template-columns: 1fr; gap: 32px; max-width: 960px; }
        @media (min-width: 900px) { .checkout-layout { grid-template-columns: 1.4fr 1fr; } }

        .form-card, .summary-card { background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 24px; }
        .form-card h2, .summary-card h2 { font-size: 18px; margin-bottom: 18px; }

        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 7px; }
        input[type=text] {
            width: 100%; border: 1px solid var(--line); border-radius: 6px;
            padding: 10px 12px; font-size: 14px; font-family: inherit; color: var(--ink);
        }
        input:focus { outline: none; border-color: var(--amber-deep); }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .error { margin-top: 6px; font-size: 13px; color: #b42318; }

        .summary-line { display: flex; justify-content: space-between; font-size: 14px; padding: 8px 0; border-bottom: 1px solid var(--line); }
        .summary-line:last-of-type { border-bottom: none; }
        .summary-item-name { font-weight: 500; }
        .summary-item-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--line); font-family: Georgia, serif; font-size: 18px; }

        .btn { width: 100%; margin-top: 20px; background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 13px; font-size: 15px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .btn:hover { background: var(--ink-light); }
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
                <h1>Checkout</h1>
            </header>

            <main>
                <div class="checkout-layout">
                    <div class="form-card">
                        <h2>Shipping details</h2>
                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf

                            <div class="field">
                                <label for="shipping_name">Full name</label>
                                <input id="shipping_name" type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}" required>
                                @error('shipping_name') <p class="error">{{ $message }}</p> @enderror
                            </div>

                            <div class="field">
                                <label for="shipping_phone">Phone number</label>
                                <input id="shipping_phone" type="text" name="shipping_phone" value="{{ old('shipping_phone') }}" required>
                                @error('shipping_phone') <p class="error">{{ $message }}</p> @enderror
                            </div>

                            <div class="field">
                                <label for="shipping_address">Address</label>
                                <input id="shipping_address" type="text" name="shipping_address" value="{{ old('shipping_address') }}" required>
                                @error('shipping_address') <p class="error">{{ $message }}</p> @enderror
                            </div>

                            <div class="row-2">
                                <div class="field">
                                    <label for="shipping_city">City</label>
                                    <input id="shipping_city" type="text" name="shipping_city" value="{{ old('shipping_city') }}" required>
                                    @error('shipping_city') <p class="error">{{ $message }}</p> @enderror
                                </div>
                                <div class="field">
                                    <label for="shipping_postal_code">Postal code</label>
                                    <input id="shipping_postal_code" type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required>
                                    @error('shipping_postal_code') <p class="error">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn">Place order</button>
                        </form>
                    </div>

                    <div class="summary-card">
                        <h2>Order summary</h2>
                        @foreach ($items as $cartItem)
                            <div class="summary-line">
                                <div>
                                    <p class="summary-item-name">{{ $cartItem->product->name }} &times; {{ $cartItem->quantity }}</p>
                                    <p class="summary-item-meta">{{ $cartItem->product->seller->store_name ?? $cartItem->product->seller->name }}</p>
                                </div>
                                <span>${{ number_format($cartItem->lineTotal(), 2) }}</span>
                            </div>
                        @endforeach
                        <div class="summary-total">
                            <span>Total</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>