<?php

namespace App\Http\Controllers;

use App\Models\ContactBericht;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'bericht' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ContactBericht::create($data);

        return redirect()
            ->route('contact')
            ->with('succes', 'Bedankt voor je bericht! We reageren zo snel mogelijk.');
    }
}