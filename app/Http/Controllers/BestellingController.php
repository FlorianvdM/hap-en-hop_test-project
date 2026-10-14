<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Support\Winkelwagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BestellingController extends Controller
{
    public const DIEETWENSEN = [
        'vegetarisch' => 'Vegetarisch',
        'veganistisch' => 'Veganistisch',
        'glutenvrij' => 'Glutenvrij',
        'lactosevrij' => 'Lactosevrij',
        'notenallergie' => 'Notenallergie',
    ];

    public function create()
    {
        $regels = Winkelwagen::regels();

        if ($regels->isEmpty()) {
            return redirect()
                ->route('pakketten.index')
                ->with('succes', 'Je winkelwagen is leeg. Kies eerst een pakket.');
        }

        return view('bestellen.create', [
            'regels' => $regels,
            'totaal' => Winkelwagen::totaal($regels),
            'dieetwensen' => self::DIEETWENSEN,
        ]);
    }

    public function store(Request $request)
    {
        $regels = Winkelwagen::regels();

        if ($regels->isEmpty()) {
            return redirect()
                ->route('pakketten.index')
                ->with('succes', 'Je winkelwagen is leeg. Kies eerst een pakket.');
        }

        $data = $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'gewenste_datum' => ['required', 'date', 'after:today'],
            'dieetwensen' => ['nullable', 'array'],
            'dieetwensen.*' => ['string', Rule::in(array_keys(self::DIEETWENSEN))],
            'opmerking' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $regels, $request) {
            $bestelling = Bestelling::create([
                'user_id' => $request->user()?->id,
                'naam' => $data['naam'],
                'email' => $data['email'],
                'telefoon' => $data['telefoon'] ?? null,
                'gewenste_datum' => $data['gewenste_datum'],
                'dieetwensen' => $data['dieetwensen'] ?? [],
                'opmerking' => $data['opmerking'] ?? null,
                'status' => 'nieuw',
            ]);

            foreach ($regels as $regel) {
                $bestelling->regels()->create([
                    'pakket_id' => $regel->pakket->id,
                    'pakket_naam' => $regel->pakket->naam,
                    'aantal' => $regel->aantal,
                    'prijs' => $regel->pakket->prijs,
                ]);
            }
        });

        Winkelwagen::leegmaken();

        return redirect()
            ->route('pakketten.index')
            ->with('succes', 'Bedankt voor je bestelling! We nemen zo snel mogelijk contact met je op.');
    }
}