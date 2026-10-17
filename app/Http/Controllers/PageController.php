<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Teamlid;

class PageController extends Controller
{
    public function home()
    {
        return view('home', [
            'producten' => Product::where('actief', true)->orderBy('prijs')->get(),
            'reviews' => Review::where('actief', true)->latest()->take(6)->get(),
        ]);
    }

    public function info()
    {
        return view('info');
    }

    public function producten()
    {
        return view('producten', [
            'producten' => Product::where('actief', true)->orderBy('prijs')->get(),
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