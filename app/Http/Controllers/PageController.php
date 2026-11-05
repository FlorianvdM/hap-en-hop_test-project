<?php

namespace App\Http\Controllers;

use App\Models\Pakket;
use App\Models\Product;
use App\Models\Review;
use App\Models\Teamlid;

class PageController extends Controller
{
    public function home()
    {
        return view('home', [
            // De drie goedkoopste actieve pakketten voor op de homepage
            'pakketten' => Pakket::where('actief', true)->orderBy('prijs')->take(3)->get(),
            // Onder "Onze favorieten" alleen broodjes die je echt kunt bestellen
            'producten' => Product::where('actief', true)->where('bestelbaar', true)->orderBy('prijs')->get(),
            'reviews' => Review::where('actief', true)->latest()->take(6)->get(),
        ]);
    }

    public function info()
    {
        return view('info');
    }

    public function catering()
    {
        // Producten die niet bestelbaar zijn, zijn catering op aanvraag
        return view('catering', [
            'producten' => Product::where('actief', true)->where('bestelbaar', false)->orderBy('naam')->get(),
        ]);
    }

    public function producten()
    {
        return view('producten', [
            'producten' => Product::where('actief', true)->where('bestelbaar', true)->orderBy('prijs')->get(),
        ]);
    }

    public function wieZijnWij()
    {
        return view('wie-zijn-wij', [
            'teamleden' => Teamlid::where('actief', true)->orderBy('volgorde')->orderBy('id')->get(),
        ]);
    }

    public function privacy()
    {
        return view('privacy');
    }
}