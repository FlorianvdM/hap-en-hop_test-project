<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminGebruikerController extends Controller
{
    public function index()
    {
        $gebruikers = User::orderBy('name')->get();

        return view('admin.gebruikers.index', compact('gebruikers'));
    }

    public function edit(User $gebruiker)
    {
        return view('admin.gebruikers.edit', compact('gebruiker'));
    }

    public function update(Request $request, User $gebruiker)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($gebruiker->id)],
            'is_admin' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $gebruiker->name = $data['name'];
        $gebruiker->email = $data['email'];

        if (! $gebruiker->is($request->user())) {
            $gebruiker->is_admin = $request->boolean('is_admin');
        }

        if (! empty($data['password'])) {
            $gebruiker->password = $data['password'];
        }

        $gebruiker->save();

        return redirect()
            ->route('admin.gebruikers.index')
            ->with('succes', 'Gebruiker bijgewerkt.');
    }

    public function destroy(Request $request, User $gebruiker)
    {
        if ($gebruiker->is($request->user())) {
            return back()->with('fout', 'Je kunt je eigen account niet verwijderen.');
        }

        $gebruiker->delete();

        return redirect()
            ->route('admin.gebruikers.index')
            ->with('succes', 'Gebruiker verwijderd.');
    }
}