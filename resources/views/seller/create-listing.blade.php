<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New listing · Trove</title>
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
        .lead { margin-top: 6px; color: var(--muted); font-size: 14px; }
        main { padding: 32px 24px; }
        @media (min-width: 1024px) { main { padding: 32px 40px; } }

        .form-card { max-width: 520px; background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 28px; }
        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        .field .hint { display: block; margin-top: 6px; font-size: 12px; color: var(--muted); }
        input[type=text], input[type=number], select {
            width: 100%; border: 1px solid var(--line); border-radius: 6px;
            padding: 10px 12px; font-size: 14px; font-family: inherit; color: var(--ink); background: #fff;
        }
        input:focus, select:focus { outline: none; border-color: var(--amber-deep); }
        input[type=file] { width: 100%; font-size: 13px; }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .error { margin-top: 6px; font-size: 13px; color: #b42318; }

        .btn { background: var(--ink); color: var(--paper); border: none; border-radius: 6px; padding: 12px 22px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: inherit; }
        .btn:hover { background: var(--ink-light); }
        .cancel-link { margin-left: 14px; font-size: 14px; color: var(--muted); text-decoration: none; }
        .cancel-link:hover { color: var(--ink); }
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
                    <a href="{{ route('listings.create') }}" class="active">New listing</a>
                    <a href="#">Orders</a>
                    <a href="#">Settings</a>
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
                <h1>Add a new listing</h1>
                <p class="lead">This will appear on the Home page for every buyer.</p>
            </header>

            <main>
                <div class="form-card">
                    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label for="name">Item name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                            @error('name') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div class="field">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select a category</option>
                                @foreach (['Home & Living', 'Ceramics', 'Lighting', 'Textiles', 'Stationery'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div class="row-2">
                            <div class="field">
                                <label for="price">Price ($)</label>
                                <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price') }}" required>
                                @error('price') <p class="error">{{ $message }}</p> @enderror
                            </div>
                            <div class="field">
                                <label for="original_price">Original price ($)</label>
                                <input id="original_price" type="number" name="original_price" step="0.01" min="0" value="{{ old('original_price') }}">
                                <span class="hint">Leave blank if it's not on sale.</span>
                                @error('original_price') <p class="error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label for="image">Photo</label>
                            <input id="image" type="file" name="image" accept="image/*">
                            <span class="hint">JPG, PNG or WEBP, up to 4MB.</span>
                            @error('image') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="btn">Publish listing</button>
                        <a href="{{ route('dashboard') }}" class="cancel-link">Cancel</a>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>