<?php

namespace App\Http\Controllers;

use App\Mail\BestellingVerzonden;
use App\Models\Bestelling;
use App\Models\ContactBericht;
use App\Models\Pakket;
use App\Models\Product;
use App\Models\User;
use App\Support\Onderhoud;
use App\Support\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminController extends Controller
{
    public function dashboard()
    {
        $grafiek = $this->omzetPerMaand();

        return view('admin.dashboard', [
            // Alle bestellingen die nog in de database staan, ook de soft deleted bestellingen
            'totaalBestellingen' => Bestelling::withTrashed()->count(),
            'aantalPakketten' => Pakket::where('actief', true)->count(),
            'aantalGebruikers' => User::count(),
            'omzet' => $grafiek['huidigeMaand'],
            'grafiek' => $grafiek,
            'recenteBestellingen' => Bestelling::with('regels')->latest()->take(5)->get(),
        ]);
    }

    public function bestellingen(Request $request)
    {
        $zoek = trim((string) $request->query('zoek'));
        $status = $request->query('status');

        $bestellingen = Bestelling::with('regels')
            // Zoeken op naam, e-mailadres of bestelnummer (met of zonder #)
            ->when($zoek !== '', function ($query) use ($zoek) {
                $query->where(function ($query) use ($zoek) {
                    $query->where('naam', 'like', "%{$zoek}%")
                        ->orWhere('email', 'like', "%{$zoek}%");

                    if (ctype_digit(ltrim($zoek, '#'))) {
                        $query->orWhere('id', (int) ltrim($zoek, '#'));
                    }
                });
            })
            ->when(array_key_exists((string) $status, Bestelling::STATUSSEN), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bestellingen', compact('bestellingen', 'zoek', 'status'));
    }

    public function keuken(Request $request)
    {
        // ?datum=2026-11-12 toont één dag, bijvoorbeeld om alleen de donderdag af te drukken
        $datum = $request->date('datum');

        $bestellingen = Bestelling::with('regels')
            ->whereIn('status', Bestelling::KEUKENSTATUSSEN)
            ->when($datum, fn ($query) => $query->whereDate('gewenste_datum', $datum))
            ->orderBy('gewenste_datum')
            ->orderBy('id')
            ->get();

        $dagen = $bestellingen->groupBy(fn ($bestelling) => $bestelling->gewenste_datum->toDateString())
            ->map(function ($groep) {
                return [
                    'datum' => $groep->first()->gewenste_datum,
                    'bestellingen' => $groep,
                    'bezorgen' => $groep->reject->wordtOpgehaald(),
                    'pakketten' => $groep->flatMap->regels
                        ->groupBy('pakket_naam')
                        ->map(fn ($regels) => $regels->sum('aantal'))
                        ->sortKeys(),
                ];
            });

        return view('admin.keuken', compact('dagen', 'datum'));
    }

    public function bestellingenExport(Request $request): StreamedResponse
    {
        $data = $request->validate([
            'van' => ['nullable', 'date'],
            'tot' => ['nullable', 'date', 'after_or_equal:van'],
        ]);

        // Alle bestellingen die nog in de database staan, ook de verwijderde verzonden bestellingen
        $query = Bestelling::withTrashed()->with('regels');

        if (! empty($data['van'])) {
            $query->whereDate('created_at', '>=', $data['van']);
        }

        if (! empty($data['tot'])) {
            $query->whereDate('created_at', '<=', $data['tot']);
        }

        $bestandsnaam = 'bestellingen-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $uit = fopen('php://output', 'w');

            // BOM, zodat Excel tekens zoals é en × goed weergeeft
            fwrite($uit, "\xEF\xBB\xBF");

            $this->schrijfRij($uit, [
                'Bestelnummer',
                'Besteld op',
                'Naam',
                'E-mailadres',
                'Telefoon',
                'Gewenste datum',
                'Tijdvak',
                'Bestelling',
                'Aantal stuks',
                'Totaal (EUR)',
                'Dieetwensen',
                'Opmerking',
                'Status',
                'Verwijderd uit lijst op',
            ]);

            $query->chunkById(200, function ($bestellingen) use ($uit) {
                foreach ($bestellingen as $bestelling) {
                    $dieetwensen = array_map(
                        fn ($wens) => BestellingController::DIEETWENSEN[$wens] ?? $wens,
                        $bestelling->dieetwensen ?? []
                    );

                    $this->schrijfRij($uit, [
                        $bestelling->id,
                        $bestelling->created_at->format('d-m-Y H:i'),
                        $this->veilig($bestelling->naam),
                        $this->veilig($bestelling->email),
                        $this->veilig($bestelling->telefoon),
                        $bestelling->gewenste_datum->format('d-m-Y'),
                        $bestelling->tijdslot ?? '',
                        $this->veilig(
                            $bestelling->regels
                                ->map(fn ($regel) => $regel->aantal . '× ' . $regel->pakket_naam)
                                ->implode('; ')
                        ),
                        $bestelling->regels->sum('aantal'),
                        number_format($bestelling->totaal, 2, ',', ''),
                        $this->veilig(implode(', ', $dieetwensen)),
                        $this->veilig($bestelling->opmerking),
                        $bestelling->status,
                        $bestelling->deleted_at?->format('d-m-Y H:i') ?? '',
                    ]);
                }
            });

            fclose($uit);
        }, $bestandsnaam, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function bestellingStatus(Request $request, Bestelling $bestelling)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Bestelling::STATUSSEN))],
        ]);

        $oud = $bestelling->status;

        // Verzonden kan alleen als de keuken de bestelling op klaar heeft gezet, en een verzonden bestelling blijft verzonden
        if (($data['status'] === 'verzonden' && ! in_array($oud, ['klaar', 'verzonden'])) || ($oud === 'verzonden' && $data['status'] !== 'verzonden')) {
            return back()->with('fout', 'Een bestelling kan pas verzonden worden als de keuken hem op klaar heeft gezet.');
        }

        // De klant krijgt één mail: bij ophalen als de bestelling klaar is, bij bezorgen als hij verzonden is
        $mailen = $data['status'] !== $oud
            && ($data['status'] === ($bestelling->wordtOpgehaald() ? 'klaar' : 'verzonden'));

        $bestelling->update($data);

        if ($mailen) {
            try {
                Mail::to($bestelling->email)->send(new BestellingVerzonden($bestelling));
            } catch (Throwable $e) {
                report($e);

                return back()->with('fout', 'Status bijgewerkt, maar de mail naar de klant kon niet worden verstuurd. Laat de klant zelf weten dat de bestelling klaar is.');
            }
        }

        return back()->with('succes', 'Status bijgewerkt.');
    }

    public function bestellingVerwijderen(Bestelling $bestelling)
    {
        if ($bestelling->status === 'verzonden') {
            // Soft delete: uit de lijst gehaald en vanaf nu meegeteld in de omzet.
            $bestelling->delete();

            return back()->with('succes', 'Bestelling verwijderd en toegevoegd aan de omzet.');
        }

        // Nieuw of in behandeling: definitief verwijderd, telt niet mee in totaal of omzet.
        // De voorraad van pakketten en producten met een beperkte voorraad komt weer terug.
        DB::transaction(function () use ($bestelling) {
            foreach ($bestelling->regels as $regel) {
                // Een regel is een pakket OF een product; bij een leeg id gebeurt er niets
                if ($regel->pakket_id) {
                    Pakket::where('id', $regel->pakket_id)
                        ->whereNotNull('voorraad')
                        ->increment('voorraad', $regel->aantal);
                }

                if ($regel->product_id) {
                    Product::where('id', $regel->product_id)
                        ->whereNotNull('voorraad')
                        ->increment('voorraad', $regel->aantal);
                }
            }

            $bestelling->forceDelete();
        });

        return back()->with('succes', 'Bestelling verwijderd.');
    }

    public function berichten(Request $request)
    {
        $zoek = trim((string) $request->query('zoek'));
        $filter = $request->query('filter');

        $berichten = ContactBericht::query()
            ->when($zoek !== '', function ($query) use ($zoek) {
                $query->where(function ($query) use ($zoek) {
                    $query->where('naam', 'like', "%{$zoek}%")
                        ->orWhere('email', 'like', "%{$zoek}%")
                        ->orWhere('bericht', 'like', "%{$zoek}%");
                });
            })
            ->when($filter === 'ongelezen', fn ($query) => $query->where('gelezen', false))
            ->when($filter === 'catering', fn ($query) => $query->where('bericht', 'like', 'Cateringaanvraag%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.berichten', compact('berichten', 'zoek', 'filter'));
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
        return view('admin.instellingen', [
            'beheerders' => User::where('is_admin', true)->orderBy('name')->get(),
            'onderhoud' => Onderhoud::aan(),
            'qrUrl' => QrCode::url(),
            'qrLokaal' => QrCode::isLokaal(),
            'qrSvg' => QrCode::svg(),
        ]);
    }

    /** De QR-code als SVG-bestand om te downloaden (voor flyers of een sticker op de toonbank). */
    public function qrCode()
    {
        return response(QrCode::svg(), 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="hapenhop-qr-code.svg"',
        ]);
    }

    public function onderhoudWisselen()
    {
        $aanzetten = ! Onderhoud::aan();

        Onderhoud::zet($aanzetten);

        return back()->with('succes', $aanzetten
            ? 'Onderhoudsmodus staat aan: bezoekers zien nu een 404-pagina.'
            : 'Onderhoudsmodus staat uit: de website is weer bereikbaar voor iedereen.');
    }

    /**
     * Omzet van de laatste 12 maanden. Een bestelling telt mee in de maand
     * waarin hij is verwijderd (soft delete, alleen bij status verzonden).
     */
    private function omzetPerMaand(): array
    {
        $namen = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];
        $start = now()->startOfMonth()->subMonths(11);

        $perMaand = Bestelling::onlyTrashed()
            ->with('regels')
            ->where('deleted_at', '>=', $start)
            ->get()
            ->groupBy(fn ($bestelling) => $bestelling->deleted_at->format('Y-m'))
            ->map(fn ($groep) => $groep->sum(fn ($bestelling) => $bestelling->totaal));

        $maanden = [];

        for ($i = 0; $i < 12; $i++) {
            $maand = $start->copy()->addMonths($i);

            $maanden[] = [
                'maand' => $namen[$maand->month - 1],
                'jaar' => $maand->format('Y'),
                'omzet' => (float) ($perMaand[$maand->format('Y-m')] ?? 0),
            ];
        }

        $hoogste = max(array_column($maanden, 'omzet'));

        foreach ($maanden as &$maand) {
            $maand['hoogte'] = $hoogste > 0 ? round($maand['omzet'] / $hoogste * 100) : 0;
        }
        unset($maand);

        return [
            'maanden' => $maanden,
            'huidigeMaand' => $maanden[11]['omzet'],
            'totaal' => array_sum(array_column($maanden, 'omzet')),
        ];
    }

    /**
     * Schrijft één regel in het CSV-bestand (puntkomma als scheidingsteken, zoals Nederlandse Excel verwacht).
     */
    private function schrijfRij($uit, array $rij): void
    {
        fputcsv($uit, $rij, ';', '"', '');
    }

    /**
     * Voorkomt dat Excel tekst van klanten als formule uitvoert.
     * Tekst die met =, +, - of @ begint, krijgt een ' ervoor. Gewone telefoonnummers
     * zoals +31 6 12345678 laten we ongemoeid.
     */
    private function veilig(?string $waarde): string
    {
        $waarde = (string) $waarde;

        if (preg_match('/^[+\-]?[0-9][0-9 ()\-]*$/', $waarde)) {
            return $waarde;
        }

        if (preg_match('/^[=+\-@\t\r]/', $waarde)) {
            return "'" . $waarde;
        }

        return $waarde;
    }
}