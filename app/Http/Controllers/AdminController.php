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
        $omzet = Bestelling::with('pakket')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->sum(fn ($bestelling) => $bestelling->aantal * $bestelling->pakket->prijs);

        return view('admin.dashboard', [
            'totaalBestellingen' => Bestelling::count(),
            'aantalPakketten' => Pakket::count(),
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

    public function instellingen()
    {
        return view('admin.instellingen');
    }

    public function binnenkort(string $pagina)
    {
        $titels = [
            'pakketten' => 'Pakketten',
            'producten' => 'Producten',
            'gebruikers' => 'Gebruikers',
        ];

        abort_unless(isset($titels[$pagina]), 404);

        return view('admin.binnenkort', ['titel' => $titels[$pagina]]);
    }
}