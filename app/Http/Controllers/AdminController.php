<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\ContactBericht;
use App\Models\Pakket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // withTrashed(): ook verwijderde verzonden bestellingen tellen mee in de cijfers.
        // Verwijderde bestellingen met een andere status bestaan niet meer in de database.
        $omzet = Bestelling::withTrashed()
            ->with('pakket')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->sum(fn ($bestelling) => $bestelling->aantal * $bestelling->pakket->prijs);

        return view('admin.dashboard', [
            'totaalBestellingen' => Bestelling::withTrashed()->count(),
            'aantalPakketten' => Pakket::where('actief', true)->count(),
            'aantalGebruikers' => User::count(),
            'omzet' => $omzet,
            'recenteBestellingen' => Bestelling::with('pakket')->latest()->take(5)->get(),
        ]);
    }

    public function bestellingen()
    {
        $bestellingen = Bestelling::with('pakket')->latest()->get();

        return view('admin.bestellingen', compact('bestellingen'));
    }

    public function bestellingStatus(Request $request, Bestelling $bestelling)
    {
        $data = $request->validate([
            'status' => ['required', 'in:nieuw,in behandeling,verzonden'],
        ]);

        $bestelling->update($data);

        return back()->with('succes', 'Status bijgewerkt.');
    }

    public function bestellingVerwijderen(Bestelling $bestelling)
    {
        if ($bestelling->status === 'verzonden') {
            // Verdwijnt uit de lijst, maar blijft meetellen in totaal en omzet.
            $bestelling->delete();

            return back()->with('succes', 'Bestelling verwijderd. Deze blijft wel meetellen in het totaal en de omzet.');
        }

        // Nieuw of in behandeling: echt verwijderen, ook uit totaal en omzet.
        $bestelling->forceDelete();

        return back()->with('succes', 'Bestelling verwijderd.');
    }

    public function berichten()
    {
        $berichten = ContactBericht::latest()->get();

        return view('admin.berichten', compact('berichten'));
    }

    public function berichtGelezen(ContactBericht $bericht)
    {
        $bericht->update(['gelezen' => true]);

        return back()->with('succes', 'Bericht gemarkeerd als gelezen.');
    }

    public function berichtVerwijderen(ContactBericht $bericht)
    {
        if (! $bericht->gelezen) {
            return back()->with('fout', 'Markeer het bericht eerst als gelezen voordat je het verwijdert.');
        }

        $bericht->delete();

        return back()->with('succes', 'Bericht verwijderd.');
    }

    public function instellingen()
    {
        return view('admin.instellingen');
    }
}