<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('onthouden'))) {
            $request->session()->regenerate();

            return Auth::user()->is_admin
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pakketten.index');
        }

        return back()
            ->withErrors(['email' => 'Het e-mailadres of wachtwoord klopt niet.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('pakketten.index');
    }

    public function showLogout()
    {
        return view('auth.uitloggen');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showAccountVerwijderen(Request $request)
    {
        // Beheerders verwijderen we niet zo: eerst de rol wijzigen (zie de bedrijfsregels)
        if ($request->user()->is_admin) {
            return redirect()->route('mijn-bestellingen')
                ->with('fout', 'Een beheerdersaccount kun je niet zelf verwijderen. Laat eerst je rol wijzigen.');
        }

        return view('auth.account-verwijderen');
    }

    /** De klant verwijdert zijn eigen account (AVG). Bestellingen blijven voor de administratie, zonder koppeling. */
    public function accountVerwijderen(Request $request)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('mijn-bestellingen')
                ->with('fout', 'Een beheerdersaccount kun je niet zelf verwijderen. Laat eerst je rol wijzigen.');
        }

        $request->validate([
            'wachtwoord' => ['required', 'current_password'],
        ], [
            'wachtwoord.current_password' => 'Dit wachtwoord klopt niet.',
        ]);

        Auth::logout();

        DB::transaction(function () use ($user) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            $user->delete(); // bestellingen.user_id wordt leeg (nullOnDelete)
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('succes', 'Je account is verwijderd.');
    }
}
