<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoSessionController extends Controller
{
    public const EMAIL = 'visita@clientmanager.example.test';

    public function __invoke(Request $request): RedirectResponse
    {
        $visitor = User::where('email', self::EMAIL)->first();

        abort_unless($visitor, 503, 'La demo todavía no está disponible.');

        Auth::login($visitor);
        $request->session()->regenerate();

        return redirect()->route('clientes.index');
    }
}
