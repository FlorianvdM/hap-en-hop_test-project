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
            'actief' => ['required', 'boolean'],
            'afbeelding' => Afbeeldingen::REGEL,
        ]);
    }
}