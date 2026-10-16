<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use Illuminate\Http\Request;

class MijnBestellingenController extends Controller
{
    public function index(Request $request)
    {
        // withTrashed(): verzonden bestellingen die de beheerder uit zijn lijst heeft gehaald,
        // blijven voor de klant gewoon zichtbaar.
        $bestellingen = Bestelling::withTrashed()
            ->with('regels')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('mijn-bestellingen.index', compact('bestellingen'));
    }
}