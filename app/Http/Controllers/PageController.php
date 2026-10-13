<?php

namespace App\Http\Controllers;

use App\Models\Product;

class PageController extends Controller
{
    public function home()
    {
        $producten = Product::where('actief', true)->orderBy('prijs')->get();

        return view('home', compact('producten'));
    }

    public function info()
    {
        return view('info');
    }
}