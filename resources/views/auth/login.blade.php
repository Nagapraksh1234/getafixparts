<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Trove</title>
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
            padding: 48px 24px;
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

        .status-msg { margin-top: 24px; font-size: 14px; color: var(--amber-deep); background: rgba(201,138,60,0.1); border: 1px solid rgba(201,138,60,0.3); border-radius: 4px; padding: 8px 12px; }

        .tabs { margin-top: 32px; display: flex; border-bottom: 1px solid var(--line); }
        .tab { flex: 1; padding-bottom: 12px; text-align: center; font-size: 14px; font-weight: 500; background: none; border: none; border-bottom: 2px solid transparent; cursor: pointer; color: var(--muted); }
        .tab.active { color: var(--ink); border-bottom-color: var(--amber); }

        form { margin-top: 28px; }
        .field { margin-bottom: 22px; }
        .field label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        .field-row { display: flex; align-items: center; justify-content: space-between; }
        .field-row a { font-size: 14px; color: var(--muted); text-decoration: none; }
        .field-row a:hover { color: var(--ink); }
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

        .remember { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted); margin-bottom: 22px; }

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
                <h1 class="headline">A marketplace where every sale has two sides worth getting right.</h1>
                <p class="subtext">Sign in to track orders as a buyer, manage a storefront as a seller, or both — one account, two ways to trade.</p>
            </div>
            <dl class="ledger">
                <div><dt>Active sellers</dt><dd>12,480</dd></div>
                <div><dt>Orders today</dt><dd>3,902</dd></div>
                <div><dt>Categories</dt><dd>64</dd></div>
            </dl>
        </div>

        <div class="form-panel">
            <div class="card">
                <h2>Welcome back</h2>
                <p class="lead">Sign in to continue to your account.</p>

                @if (session('status'))
                    <div class="status-msg">{{ session('status') }}</div>
                @endif

                <div class="tabs" role="tablist" aria-label="Continue as">
                    <button type="button" data-role-tab="buyer" class="tab active" role="tab" aria-selected="true">Buying</button>
                    <button type="button" data-role-tab="seller" class="tab" role="tab" aria-selected="false">Selling</button>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="intent" id="intent" value="buyer">

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <label for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">Forgot it?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password">
                        @error('password') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <label class="remember">
                        <input type="checkbox" name="remember">
                        Keep me signed in
                    </label>

                    <button type="submit" class="btn">Sign in</button>
                </form>

                <p class="footnote">New to Trove? <a href="{{ route('register') }}">Create an account</a></p>
            </div>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('[data-role-tab]');
        const intent = document.getElementById('intent');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
                intent.value = tab.dataset.roleTab;
            });
        });
    </script>
</body>
</html>