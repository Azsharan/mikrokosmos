<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        return view('shop.account', [
            'user' => auth('shop')->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user('shop');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('shop_users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'newsletter_opt_in' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'newsletter_opt_in' => (bool) ($validated['newsletter_opt_in'] ?? false),
        ]);

        return back()->with('account_status', __('Tu información se actualizó correctamente.'));
    }
}
