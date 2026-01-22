<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        return view('shop.account', [
            'user' => auth('shop')->user(),
        ]);
    }
}
