<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::latest()->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('admin.reviews.form', [
            'review' => new Review(['actief' => true, 'beoordeling' => 5]),
        ]);
    }

    public function store(Request $request)
    {
        Review::create($this->gevalideerd($request));

        return redirect()
            ->route('admin.reviews.index')
            ->with('succes', 'Review toegevoegd.');
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.form', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $review->update($this->gevalideerd($request));

        return redirect()
            ->route('admin.reviews.index')
            ->with('succes', 'Review bijgewerkt.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('succes', 'Review verwijderd.');
    }

    private function gevalideerd(Request $request): array
    {
        return $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'tekst' => ['required', 'string', 'max:600'],
            'beoordeling' => ['required', 'integer', 'min:1', 'max:5'],
            'actief' => ['required', 'boolean'],
        ]);
    }
}