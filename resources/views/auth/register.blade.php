<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account · Trove</title>
    <style>
        :root {
            --ink: #13221D;
            --ink-light: #1E332B;
            --paper: #F4F5F1;
            --line: #DBDDD4;
            --amber: #C98A3C;
            --amber-deep: #A66D28;
            --muted: #6B7568;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
        }
        h1, h2 { font-family: Georgia, 'Times New Roman', serif; margin: 0; }

        .wrap { min-height: 100vh; display: flex; }
        .brand-panel {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            background: var(--ink);
            color: var(--paper);
            width: 50%;
            padding: 48px 56px;
        }
        .form-panel {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }
        @media (min-width: 1024px) {
            .brand-panel { display: flex; }
            .form-panel { width: 50%; }
        }

        .logo { display: flex; align-items: center; gap: 8px; font-size: 18px; font-weight: 600; text-decoration: none; color: inherit; }
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--amber); }

        .headline { font-size: 40px; line-height: 1.15; font-weight: 500; max-width: 420px; }
        .subtext { margin-top: 18px; color: rgba(244,245,241,0.6); font-size: 15px; line-height: 1.6; max-width: 380px; }

        .ledger { border-top: 1px solid rgba(244,245,241,0.15); padding-top: 24px; display: flex; gap: 24px; }
        .ledger dt { font-size: 13px; color: rgba(244,245,241,0.45); margin: 0; }
        .ledger dd { margin: 6px 0 0; font-family: Georgia, serif; font-size: 24px; }

        .card { width: 100%; max-width: 380px; }
        .card h2 { font-size: 28px; font-weight: 500; }
        .card .lead { margin-top: 8px; color: var(--muted); font-size: 15px; }

        form { margin-top: 26px; }
        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        input[type=email], input[type=password], input[type=text] {
            width: 100%;
            border: none;
            border-bottom: 1px solid var(--line);
            background: transparent;
            padding: 8px 0;
            font-size: 15px;
            font-family: inherit;
            color: var(--ink);
        }
        input:focus { outline: none; border-bottom-color: var(--amber-deep); }
        .error { margin-top: 6px; font-size: 13px; color: #b42318; }

        .role-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .role-card { position: relative; display: block; cursor: pointer; border: 1px solid var(--line); border-radius: 6px; padding: 12px 16px; }
        .role-card.selected { border-color: var(--amber); background: rgba(201,138,60,0.06); }
        .role-card input { position: absolute; opacity: 0; }
        .role-title { display: block; font-size: 14px; font-weight: 500; }
        .role-desc { display: block; margin-top: 2px; font-size: 12px; color: var(--muted); }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .hidden { display: none; }

        .btn { width: 100%; background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 13px; font-size: 15px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .btn:hover { background: var(--ink-light); }

        .footnote { margin-top: 28px; font-size: 14px; color: var(--muted); }
        .footnote a { color: var(--ink); font-weight: 500; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="brand-panel">
            <a href="/" class="logo"><span class="dot"></span>Trove</a>
            <div>
                <h1 class="headline">Bring something to sell, or come looking for something good.</h1>
                <p class="subtext">One account works for both. Sellers get a storefront and order tools; buyers get a single place to track everything they've bought.</p>
            </div>
            <dl class="ledger">
                <div><dt>Active sellers</dt><dd>12,480</dd></div>
                <div><dt>Orders today</dt><dd>3,902</dd></div>
                <div><dt>Categories</dt><dd>64</dd></div>
            </dl>
        </div>

        <div class="form-panel">
            <div class="card">
                <h2>Create your account</h2>
                <p class="lead">Takes about a minute.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="field">
                        <label>I'm here to</label>
                        <div class="role-group" role="radiogroup" aria-label="Account type">
                            <label class="role-card selected" data-role-card>
                                <input type="radio" name="role" value="buyer" checked>
                                <span class="role-title">Buy</span>
                                <span class="role-desc">Shop and track orders</span>
                            </label>
                            <label class="role-card" data-role-card>
                                <input type="radio" name="role" value="seller">
                                <span class="role-title">Sell</span>
                                <span class="role-desc">List items, manage a store</span>
                            </label>
                        </div>
                        @error('role') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field hidden" id="store-name-field">
                        <label for="store_name">Store name</label>
                        <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}" autocomplete="organization" placeholder="What buyers will see on your listings">
                        @error('store_name') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password">
                            @error('password') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirm</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn">Create account</button>
                </form>

                <p class="footnote">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>

    <script>
        const roleCards = document.querySelectorAll('[data-role-card]');
        const storeField = document.getElementById('store-name-field');
        const storeInput = document.getElementById('store_name');

        roleCards.forEach(card => {
            card.addEventListener('click', () => {
                roleCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                const isSeller = card.querySelector('input').value === 'seller';
                storeField.classList.toggle('hidden', !isSeller);
                if (!isSeller) storeInput.value = '';
            });
        });
    </script>
</body>
</html>