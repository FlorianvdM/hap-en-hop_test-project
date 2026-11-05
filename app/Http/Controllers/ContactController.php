<?php

namespace App\Http\Controllers;

use App\Models\ContactBericht;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public const GELEGENHEDEN = [
        'werk' => 'Lunch op het werk',
        'verjaardag' => 'Verjaardag of feest',
        'vergadering' => 'Vergadering of training',
        'anders' => 'Iets anders',
    ];

    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        if ($this->isSpam($request)) {
            return $this->bedankt('contact');
        }

        $data = $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'bericht' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ContactBericht::create($data);

        return $this->bedankt('contact');
    }

    /** Cateringaanvraag: komt als bericht binnen bij de beheerders, met alle gegevens bij elkaar. */
    public function catering(Request $request)
    {
        if ($this->isSpam($request)) {
            return $this->bedankt('catering');
        }

        $data = $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'datum' => ['required', 'date', 'after:today'],
            'personen' => ['required', 'integer', 'min:1', 'max:1000'],
            'gelegenheid' => ['required', Rule::in(array_keys(self::GELEGENHEDEN))],
            'wensen' => ['nullable', 'string', 'max:1500'],
        ]);

        $regels = [
            'Cateringaanvraag',
            'Datum: ' . date('d-m-Y', strtotime($data['datum'])),
            'Aantal personen: ' . $data['personen'],
            'Gelegenheid: ' . self::GELEGENHEDEN[$data['gelegenheid']],
        ];

        if (! empty($data['telefoon'])) {
            $regels[] = 'Telefoon: ' . $data['telefoon'];
        }

        if (! empty($data['wensen'])) {
            $regels[] = '';
            $regels[] = 'Wensen: ' . $data['wensen'];
        }

        ContactBericht::create([
            'naam' => $data['naam'],
            'email' => $data['email'],
            'bericht' => implode("\n", $regels),
        ]);

        return $this->bedankt('catering');
    }

    /** Het onzichtbare veld "website" vullen alleen bots in. Die krijgen dezelfde melding, maar er wordt niets opgeslagen. */
    private function isSpam(Request $request): bool
    {
        return filled($request->input('website'));
    }

    private function bedankt(string $route)
    {
        return redirect()
            ->route($route)
            ->with('succes', 'Bedankt voor je bericht! We reageren zo snel mogelijk.');
    }
}
