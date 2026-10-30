<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Afbeeldingen;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index()
    {
        $producten = Product::orderBy('prijs')->get();

        return view('admin.producten.index', compact('producten'));
    }

    public function create()
    {
        return view('admin.producten.form', [
            'product' => new Product(['actief' => true]),
        ]);
    }

    public function store(Request $request)
    {
        Product::create(Afbeeldingen::opslaan($request, $this->gevalideerd($request), null, 'producten'));

        return redirect()
            ->route('admin.producten.index')
            ->with('succes', 'Product toegevoegd.');
    }

    public function edit(Product $product)
    {
        return view('admin.producten.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update(Afbeeldingen::opslaan($request, $this->gevalideerd($request), $product->afbeelding, 'producten'));

        return redirect()
            ->route('admin.producten.index')
            ->with('succes', 'Product bijgewerkt.');
    }

    public function destroy(Product $product)
    {
        // Een product dat in bestellingen voorkomt, bewaren we: de bestelgeschiedenis blijft dan kloppen
        if ($product->bestelregels()->exists()) {
            return back()->with('fout', 'Dit product komt voor in bestellingen en kan niet worden verwijderd. Zet het op "Verborgen" om het van de website te halen.');
        }

        Afbeeldingen::verwijderen($product->afbeelding);
        $product->delete();

        return redirect()
            ->route('admin.producten.index')
            ->with('succes', 'Product verwijderd.');
    }

    private function gevalideerd(Request $request): array
    {
        return $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'beschrijving' => ['nullable', 'string', 'max:255'],
            'prijs' => ['required', 'numeric', 'min:0', 'max:99999'],
            'voorraad' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'bestelbaar' => ['sometimes', 'boolean'],
            'actief' => ['required', 'boolean'],
            'afbeelding' => Afbeeldingen::REGEL,
        ]);
    }
}