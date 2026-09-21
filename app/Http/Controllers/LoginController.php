<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'intent' => ['nullable', 'in:buyer,seller'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect('/login');
        }

        return $user->role === 'seller'
            ? view('seller.dashboard', $this->sellerData($user))
            : view('buyer.dashboard', $this->buyerData($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function buyerData(User $user): array
    {
        return [
            'stats' => [
                ['label' => 'Active orders', 'value' => '3'],
                ['label' => 'Total spent', 'value' => '$1,284'],
                ['label' => 'Saved items', 'value' => '12'],
                ['label' => 'Open messages', 'value' => '2'],
            ],
            'orders' => [
                ['id' => '#TR-10482', 'item' => 'Walnut writing desk', 'seller' => 'Amber & Oak', 'date' => 'Sep 18', 'total' => '$420.00', 'status' => 'In transit'],
                ['id' => '#TR-10471', 'item' => 'Ceramic pour-over set', 'seller' => 'Kiln Studio', 'date' => 'Sep 14', 'total' => '$68.00', 'status' => 'Delivered'],
                ['id' => '#TR-10459', 'item' => 'Wool throw blanket', 'seller' => 'Northfield Textiles', 'date' => 'Sep 10', 'total' => '$96.00', 'status' => 'Delivered'],
                ['id' => '#TR-10440', 'item' => 'Brass desk lamp', 'seller' => 'Foundry Goods', 'date' => 'Sep 6', 'total' => '$142.00', 'status' => 'Processing'],
            ],
            'categories' => ['Home & Living', 'Ceramics', 'Lighting', 'Textiles', 'Stationery'],
        ];
    }

    protected function sellerData(User $user): array
    {
        return [
            'stats' => [
                ['label' => 'Revenue this month', 'value' => '$8,420', 'delta' => '+12%'],
                ['label' => 'Orders to fulfill', 'value' => '6', 'delta' => null],
                ['label' => 'Active listings', 'value' => '34', 'delta' => null],
                ['label' => 'Store views', 'value' => '2,150', 'delta' => '+4%'],
            ],
            'orders' => [
                ['id' => '#TR-10482', 'item' => 'Walnut writing desk', 'buyer' => 'M. Alvarez', 'date' => 'Sep 18', 'total' => '$420.00', 'status' => 'Pending'],
                ['id' => '#TR-10475', 'item' => 'Oak side table', 'buyer' => 'J. Osei', 'date' => 'Sep 17', 'total' => '$210.00', 'status' => 'Pending'],
                ['id' => '#TR-10471', 'item' => 'Bookshelf, small', 'buyer' => 'R. Kapoor', 'date' => 'Sep 14', 'total' => '$310.00', 'status' => 'Paid'],
                ['id' => '#TR-10459', 'item' => 'Coat rack', 'buyer' => 'S. Lindqvist', 'date' => 'Sep 10', 'total' => '$96.00', 'status' => 'Fulfilled'],
            ],
            'lowStock' => [
                ['name' => 'Walnut writing desk', 'left' => 1],
                ['name' => 'Brass drawer pulls (set of 4)', 'left' => 2],
                ['name' => 'Linen napkin set', 'left' => 3],
            ],
        ];
    }
}