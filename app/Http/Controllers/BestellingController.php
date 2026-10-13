<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Pakket;
use Illuminate\Http\Request;

class BestellingController extends Controller
{
    public function create(Pakket $pakket)
    {
        abort_unless($pakket->actief, 404);

        return view('bestellen.create', compact('pakket'));
    }

    public function store(Request $request, Pakket $pakket)
    {
        abort_unless($pakket->actief, 404);

        $data = $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'aantal' => ['required', 'integer', 'min:1', 'max:100'],
            'gewenste_datum' => ['required', 'date', 'after:today'],
            'opmerking' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['pakket_id'] = $pakket->id;
        $data['user_id'] = $request->user()?->id;

        Bestelling::create($data);

        return redirect()
            ->route('pakketten.index')
            ->with('succes', 'Bedankt voor je bestelling! We nemen zo snel mogelijk contact met je op.');
    }
}