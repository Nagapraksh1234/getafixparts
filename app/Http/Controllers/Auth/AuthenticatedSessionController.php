<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            // 'intent' is the Buying/Selling tab on the login page — it's only
            // a hint for where to redirect, never used to authorize anything.
            'intent' => ['nullable', 'in:buyer,seller'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return redirect()->intended($this->dashboardFor($user, $request->input('intent')));
    }

    /**
     * Log the user out of the application.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Decide where to send a user after login.
     *
     * Trusts the account's actual role first. Only falls back to the tab
     * they picked on the login form if the account can act as either
     * (e.g. role is nullable / a user can be both buyer and seller).
     */
    protected function dashboardFor(User $user, ?string $intent): string
    {
        if ($user->role === 'seller') {
            return route('seller.dashboard');
        }

        if ($user->role === 'buyer') {
            return route('buyer.dashboard');
        }

        return $intent === 'seller' ? route('seller.dashboard') : route('buyer.dashboard');
    }
}