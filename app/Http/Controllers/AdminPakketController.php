<?php

namespace App\Http\Controllers;

use App\Models\Pakket;
use Illuminate\Http\Request;

class AdminPakketController extends Controller
{
    public function index()
    {
        $pakketten = Pakket::withCount('bestelregels as bestellingen_count')
            ->orderBy('prijs')
            ->get();

        return view('admin.pakketten.index', compact('pakketten'));
    }

    public function create()
    {
        return view('admin.pakketten.form', [
            'pakket' => new Pakket(['actief' => true]),
        ]);
    }

    public function store(Request $request)
    {
        Pakket::create($this->gevalideerd($request));

        return redirect()
            ->route('admin.pakketten.index')
            ->with('succes', 'Pakket toegevoegd.');
    }

    public function edit(Pakket $pakket)
    {
        return view('admin.pakketten.form', compact('pakket'));
    }

    public function update(Request $request, Pakket $pakket)
    {
        $pakket->update($this->gevalideerd($request));

        return redirect()
            ->route('admin.pakketten.index')
            ->with('succes', 'Pakket bijgewerkt.');
    }

    public function destroy(Pakket $pakket)
    {
        if ($pakket->bestelregels()->exists()) {
            return back()->with('fout', 'Dit pakket komt voor in bestellingen en kan niet worden verwijderd. Zet het op "Verborgen" om het van de website te halen.');
        }

        $pakket->delete();

        return redirect()
            ->route('admin.pakketten.index')
            ->with('succes', 'Pakket verwijderd.');
    }

    private function gevalideerd(Request $request): array
    {
        return $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'beschrijving' => ['nullable', 'string', 'max:1000'],
            'prijs' => ['required', 'numeric', 'min:0', 'max:99999'],
            'actief' => ['required', 'boolean'],
        ]);
    }
}