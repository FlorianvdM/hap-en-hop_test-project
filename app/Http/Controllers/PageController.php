<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

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

    public function privacy()
    {
        return view('privacy');
    }
}