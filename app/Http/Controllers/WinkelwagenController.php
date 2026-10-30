<?php

namespace App\Http\Controllers;

use App\Models\Pakket;
use App\Models\Product;
use App\Support\Winkelwagen;
use Illuminate\Http\Request;

class WinkelwagenController extends Controller
{
    public function index()
    {
        $regels = Winkelwagen::regels();

        return view('winkelwagen.index', [
            'regels' => $regels,
            'totaal' => Winkelwagen::totaal($regels),
        ]);
    }

    // ---------- Pakketten ----------

    public function toevoegen(Pakket $pakket)
    {
        abort_unless($pakket->actief, 404);

        return $this->voegToe($pakket, $pakket->id);
    }

    public function wijzigen(Request $request, Pakket $pakket)
    {
        return $this->wijzigAantal($request, $pakket, $pakket->id);
    }

    public function verwijderen(Pakket $pakket)
    {
        Winkelwagen::verwijderen($pakket->id);

        return redirect()
            ->route('winkelwagen.index')
            ->with('succes', 'Pakket verwijderd uit je winkelwagen.');
    }

    // ---------- Producten (bijvoorbeeld losse broodjes) ----------

    public function productToevoegen(Product $product)
    {
        // Alleen zichtbare producten die als bestelbaar zijn aangemerkt
        abort_unless($product->actief && $product->bestelbaar, 404);

        return $this->voegToe($product, Winkelwagen::productSleutel($product->id));
    }

    public function productWijzigen(Request $request, Product $product)
    {
        return $this->wijzigAantal($request, $product, Winkelwagen::productSleutel($product->id));
    }

    public function productVerwijderen(Product $product)
    {
        Winkelwagen::verwijderen(Winkelwagen::productSleutel($product->id));

        return redirect()
            ->route('winkelwagen.index')
            ->with('succes', 'Product verwijderd uit je winkelwagen.');
    }

    // ---------- Gedeeld door pakketten en producten ----------

    /** Voegt één stuk toe, tenzij er niet genoeg voorraad is. */
    private function voegToe(Pakket|Product $item, int|string $sleutel)
    {
        $max = $item->maxBestelbaar();
        $inWagen = Winkelwagen::items()[$sleutel] ?? 0;

        if ($max !== null && $inWagen + 1 > $max) {
            return back()->with('fout', $max === 0
                ? $item->naam . ' is uitverkocht.'
                : 'Er zijn nog maar ' . $max . ' van ' . $item->naam . ' beschikbaar.');
        }

        Winkelwagen::toevoegen($sleutel);

        return back()->with('succes', $item->naam . ' is toegevoegd aan je winkelwagen.');
    }

    /** Past het aantal aan, maar nooit hoger dan de voorraad. */
    private function wijzigAantal(Request $request, Pakket|Product $item, int|string $sleutel)
    {
        $data = $request->validate([
            'aantal' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $max = $item->maxBestelbaar();

        if ($max !== null && $data['aantal'] > $max) {
            Winkelwagen::wijzigen($sleutel, max(1, $max));

            return redirect()
                ->route('winkelwagen.index')
                ->with('fout', $max === 0
                    ? $item->naam . ' is uitverkocht. Verwijder het uit je winkelwagen.'
                    : 'Er zijn nog maar ' . $max . ' van ' . $item->naam . ' beschikbaar. Het aantal is aangepast.');
        }

        Winkelwagen::wijzigen($sleutel, $data['aantal']);

        return redirect()->route('winkelwagen.index');
    }
}