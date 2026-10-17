<?php

namespace App\Http\Controllers;

use App\Models\Pakket;
use App\Support\Winkelwagen;
use Illuminate\Http\Request;

class WinkelwagenController extends Controller
{
    public function index()
    {
        $regels = Winkelwagen::regels();

        return view('winkelwagen.index', [
            'regels' => $regels,
            'totaal' => Winkelwagen::totaal($regels),
        ]);
    }

    public function toevoegen(Pakket $pakket)
    {
        abort_unless($pakket->actief, 404);

        $max = $pakket->maxBestelbaar();
        $inWagen = Winkelwagen::items()[$pakket->id] ?? 0;

        if ($max !== null && $inWagen + 1 > $max) {
            return back()->with('fout', $max === 0
                ? $pakket->naam . ' is uitverkocht.'
                : 'Er zijn nog maar ' . $max . ' van ' . $pakket->naam . ' beschikbaar.');
        }

        Winkelwagen::toevoegen($pakket->id);

        return back()->with('succes', $pakket->naam . ' is toegevoegd aan je winkelwagen.');
    }

    public function wijzigen(Request $request, Pakket $pakket)
    {
        $data = $request->validate([
            'aantal' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $max = $pakket->maxBestelbaar();

        if ($max !== null && $data['aantal'] > $max) {
            Winkelwagen::wijzigen($pakket->id, max(1, $max));

            return redirect()
                ->route('winkelwagen.index')
                ->with('fout', $max === 0
                    ? $pakket->naam . ' is uitverkocht. Verwijder het uit je winkelwagen.'
                    : 'Er zijn nog maar ' . $max . ' van ' . $pakket->naam . ' beschikbaar. Het aantal is aangepast.');
        }

        Winkelwagen::wijzigen($pakket->id, $data['aantal']);

        return redirect()->route('winkelwagen.index');
    }

    public function verwijderen(Pakket $pakket)
    {
        Winkelwagen::verwijderen($pakket->id);

        return redirect()
            ->route('winkelwagen.index')
            ->with('succes', 'Pakket verwijderd uit je winkelwagen.');
    }
}