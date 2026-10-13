<?php

namespace App\Http\Controllers;

use App\Models\Pakket;

class PakketController extends Controller
{
    public function index()
    {
        $pakketten = Pakket::orderBy('naam')->get();

        return view('pakketten.index', compact('pakketten'));
    }
}