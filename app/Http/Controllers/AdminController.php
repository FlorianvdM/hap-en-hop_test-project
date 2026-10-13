<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\ContactBericht;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $bestellingen = Bestelling::with('pakket')->latest()->get();
        $berichten = ContactBericht::latest()->get();

        $nieuweBestellingen = $bestellingen->where('status', 'nieuw')->count();
        $ongelezenBerichten = $berichten->where('gelezen', false)->count();

        return view('admin.dashboard', compact(
            'bestellingen',
            'berichten',
            'nieuweBestellingen',
            'ongelezenBerichten'
        ));
    }

    public function bestellingStatus(Request $request, Bestelling $bestelling)
    {
        $data = $request->validate([
            'status' => ['required', 'in:nieuw,in behandeling,afgerond'],
        ]);

        $bestelling->update($data);

        return back()->with('succes', 'Status bijgewerkt.');
    }

    public function berichtGelezen(ContactBericht $bericht)
    {
        $bericht->update(['gelezen' => true]);

        return back()->with('succes', 'Bericht gemarkeerd als gelezen.');
    }
}