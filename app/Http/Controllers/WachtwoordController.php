<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

class WachtwoordController extends Controller
{
    public function aanvragen()
    {
        return view('auth.wachtwoord-vergeten');
    }

    public function versturen(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['email' => 'Het versturen van de e-mail is niet gelukt. Probeer het later nog eens.']);
        }

        // Altijd dezelfde melding, zodat niemand kan uitzoeken welke e-mailadressen een account hebben
        return back()->with('succes', 'Hoort dit e-mailadres bij een account? Dan hebben we je een e-mail gestuurd met een link om een nieuw wachtwoord in te stellen.');
    }

    public function formulier(Request $request, string $token)
    {
        return view('auth.wachtwoord-resetten', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function bijwerken(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($data, function (User $user, string $wachtwoord) {
            $user->forceFill([
                'password' => $wachtwoord,
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('succes', 'Je wachtwoord is gewijzigd. Je kunt nu inloggen.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Deze link is ongeldig of verlopen. Vraag een nieuwe aan.']);
    }
}