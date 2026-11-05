<?php

namespace App\Http\Controllers;

use App\Mail\BestellingBevestiging;
use App\Mail\NieuweBestelling;
use App\Models\Bestelling;
use App\Models\Pakket;
use App\Models\Product;
use App\Support\Winkelwagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class BestellingController extends Controller
{
    public const DIEETWENSEN = [
        'vegetarisch' => 'Vegetarisch',
        'veganistisch' => 'Veganistisch',
        'halal' => 'Halal',
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
            'broodjesDagen' => self::broodjesDagen($regels),
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
            // Met broodjes alleen een donderdag uit de lijst, anders elke dag vanaf morgen
            'gewenste_datum' => ($dagen = self::broodjesDagen($regels)) === null
                ? ['required', 'date', 'after:today']
                : ['required', Rule::in($dagen)],
            'levering' => ['required', Rule::in(['ophalen', 'bezorgen'])],
            'straat' => ['required_if:levering,bezorgen', 'nullable', 'string', 'max:150'],
            'postcode' => ['required_if:levering,bezorgen', 'nullable', 'string', 'max:10'],
            'plaats' => ['required_if:levering,bezorgen', 'nullable', 'string', 'max:100'],
            'dieetwensen' => ['nullable', 'array'],
            'dieetwensen.*' => ['string', Rule::in(array_keys(self::DIEETWENSEN))],
            'opmerking' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $bestelling = DB::transaction(function () use ($data, $regels, $request) {
                $this->boekVoorraadAf($regels);

                return $this->maakBestelling($data, $regels, $request);
            });
        } catch (\DomainException $e) {
            return redirect()->route('winkelwagen.index')->with('fout', $e->getMessage());
        }

        Winkelwagen::leegmaken();

        $bevestigingVerstuurd = $this->verstuurMails($bestelling->load('regels'));

        $melding = $bevestigingVerstuurd
            ? 'Bedankt voor je bestelling! Je ontvangt een bevestiging per e-mail en we nemen zo snel mogelijk contact met je op.'
            : 'Bedankt voor je bestelling! We nemen zo snel mogelijk contact met je op.';

        return redirect()
            ->route('pakketten.index')
            ->with('succes', $melding);
    }

    /**
     * Broodjes zijn er alleen op donderdag: bestellen kan tot 10.30 uur op de dag zelf.
     * Geeft de donderdagen die je kunt kiezen (Y-m-d), of null als er geen broodjes in de winkelwagen zitten.
     * Een pakket moet minstens een dag van tevoren besteld worden, dus dan valt vandaag af.
     * shortcut: alleen de komende 8 donderdagen, verder vooruit kan via het contactformulier.
     */
    public static function broodjesDagen($regels): ?array
    {
        if (! $regels->contains('soort', 'product')) {
            return null;
        }

        $nu = now();
        $vandaagKan = $nu->isThursday()
            && $nu->format('H:i') < '10:30'
            && ! $regels->contains('soort', 'pakket');

        $dag = $vandaagKan ? $nu->copy()->startOfDay() : $nu->copy()->next(\Carbon\Carbon::THURSDAY);

        $dagen = [];
        for ($i = 0; $i < 8; $i++) {
            $dagen[] = $dag->copy()->addWeeks($i)->format('Y-m-d');
        }

        return $dagen;
    }

    /**
     * Controleert de voorraad en boekt die af, voor pakketten en producten.
     * Ze worden vergrendeld, zodat twee klanten tegelijk niet allebei het laatste exemplaar kunnen kopen.
     * Gooit een DomainException als er te weinig is.
     */
    private function boekVoorraadAf($regels): void
    {
        // De id's van de pakketten en producten in de winkelwagen
        $pakketIds = $regels->where('soort', 'pakket')->map(fn ($regel) => $regel->item->id)->all();
        $productIds = $regels->where('soort', 'product')->map(fn ($regel) => $regel->item->id)->all();

        $pakketten = Pakket::whereIn('id', $pakketIds)->lockForUpdate()->get()->keyBy('id');
        $producten = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

        // Eerst alles controleren...
        foreach ($regels as $regel) {
            $item = $this->actueel($regel, $pakketten, $producten);

            if ($item->voorraadBeperkt() && $item->voorraad < $regel->aantal) {
                throw new \DomainException($item->voorraad <= 0
                    ? "{$item->naam} is helaas uitverkocht. Verwijder het uit je winkelwagen om verder te gaan."
                    : "Er zijn nog maar {$item->voorraad} van {$item->naam} beschikbaar. Pas het aantal aan om verder te gaan.");
            }
        }

        // ...en pas daarna de voorraad verlagen
        foreach ($regels as $regel) {
            $item = $this->actueel($regel, $pakketten, $producten);

            if ($item->voorraadBeperkt()) {
                $item->decrement('voorraad', $regel->aantal);
            }
        }
    }

    /** Zoekt het vergrendelde pakket of product dat bij een winkelwagenregel hoort. */
    private function actueel($regel, $pakketten, $producten): Pakket|Product
    {
        return $regel->soort === 'pakket'
            ? $pakketten[$regel->item->id]
            : $producten[$regel->item->id];
    }

    private function maakBestelling(array $data, $regels, Request $request): Bestelling
    {
        $bestelling = Bestelling::create([
            'user_id' => $request->user()?->id,
            'naam' => $data['naam'],
            'email' => $data['email'],
            'telefoon' => $data['telefoon'] ?? null,
            'gewenste_datum' => $data['gewenste_datum'],
            'levering' => $data['levering'],
            // Een adres bewaren we alleen als er echt bezorgd wordt
            'straat' => $data['levering'] === 'bezorgen' ? $data['straat'] : null,
            'postcode' => $data['levering'] === 'bezorgen' ? $data['postcode'] : null,
            'plaats' => $data['levering'] === 'bezorgen' ? $data['plaats'] : null,
            'dieetwensen' => $data['dieetwensen'] ?? [],
            'opmerking' => $data['opmerking'] ?? null,
            'status' => 'nieuw',
        ]);

        foreach ($regels as $regel) {
            // Een regel hoort bij een pakket OF een product; de naam en prijs bewaren we van dit moment
            $bestelling->regels()->create([
                'pakket_id' => $regel->soort === 'pakket' ? $regel->item->id : null,
                'product_id' => $regel->soort === 'product' ? $regel->item->id : null,
                'pakket_naam' => $regel->item->naam,
                'aantal' => $regel->aantal,
                'prijs' => $regel->item->prijs,
            ]);
        }

        return $bestelling;
    }

    /**
     * Stuurt de bevestiging naar de klant en de melding naar Hap & Hop.
     * Gaat het mailen mis, dan blijft de bestelling gewoon bestaan en komt de fout in het logboek.
     * Geeft terug of de bevestiging aan de klant is verstuurd (of klaargezet in de wachtrij).
     */
    private function verstuurMails(Bestelling $bestelling): bool
    {
        $pauze = (int) config('hapenhop.mail_pauze');

        // Op de achtergrond versturen: de klant hoeft niet te wachten, een losse "queue worker" verstuurt de mails.
        // De melding voor Hap & Hop wordt met vertraging klaargezet (zie HAPHOP_MAIL_PAUZE).
        if (config('hapenhop.mail_wachtrij')) {
            try {
                Mail::to($bestelling->email)->queue(new BestellingBevestiging($bestelling));
                Mail::to(config('hapenhop.bestelmelding_naar'))->later(now()->addSeconds($pauze), new NieuweBestelling($bestelling));

                return true;
            } catch (Throwable $e) {
                report($e);

                return false;
            }
        }

        $bevestigingVerstuurd = false;

        try {
            Mail::to($bestelling->email)->send(new BestellingBevestiging($bestelling));
            $bevestigingVerstuurd = true;
        } catch (Throwable $e) {
            report($e);
        }

        // Testomgevingen zoals de gratis Mailtrap-sandbox weigeren twee mails vlak na elkaar.
        // Met HAPHOP_MAIL_PAUZE in .env wachten we even voordat de tweede mail wordt verstuurd.
        if ($pauze > 0) {
            sleep($pauze);
        }

        try {
            Mail::to(config('hapenhop.bestelmelding_naar'))->send(new NieuweBestelling($bestelling));
        } catch (Throwable $e) {
            report($e);
        }

        return $bevestigingVerstuurd;
    }
}