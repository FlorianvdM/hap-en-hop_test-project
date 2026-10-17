<?php

namespace App\Http\Controllers;

use App\Mail\BestellingBevestiging;
use App\Mail\NieuweBestelling;
use App\Models\Bestelling;
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

        $bestelling = DB::transaction(function () use ($data, $regels, $request) {
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

            return $bestelling;
        });

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