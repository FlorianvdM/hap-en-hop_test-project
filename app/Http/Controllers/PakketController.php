<?php

namespace App\Http\Controllers;

use App\Models\Pakket;
use App\Support\PakketAdviseur;
use Illuminate\Http\Request;

class PakketController extends Controller
{
    public function index()
    {
        $pakketten = Pakket::where('actief', true)->orderBy('prijs')->get();

        return view('pakketten.index', compact('pakketten'));
    }

    public function advies(Request $request, PakketAdviseur $adviseur)
    {
        $data = $request->validate([
            'aantal' => ['required', 'integer', 'min:1', 'max:500'],
            'wensen' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $adviseur->beschikbaar()) {
            return back()->withInput()->with('fout', 'Het pakketadvies is nu niet beschikbaar. Kijk gerust bij de pakketten of stuur ons een bericht.');
        }

        try {
            $advies = $adviseur->advies((int) $data['aantal'], $data['wensen'] ?? null);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('fout', 'Het advies maken is niet gelukt. Probeer het later opnieuw of stuur ons een bericht.');
        }

        return back()->withInput()->with('advies', $advies);
    }
}
