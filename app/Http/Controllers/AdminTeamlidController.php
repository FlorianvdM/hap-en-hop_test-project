<?php

namespace App\Http\Controllers;

use App\Models\Teamlid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTeamlidController extends Controller
{
    public function index()
    {
        $teamleden = Teamlid::orderBy('volgorde')->orderBy('id')->get();

        return view('admin.team.index', compact('teamleden'));
    }

    public function create()
    {
        return view('admin.team.form', [
            'teamlid' => new Teamlid(['actief' => true, 'volgorde' => 0]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->gevalideerd($request);

        Teamlid::create($this->metFoto($request, $data));

        return redirect()
            ->route('admin.team.index')
            ->with('succes', 'Opdrachtgever toegevoegd.');
    }

    public function edit(Teamlid $team)
    {
        return view('admin.team.form', ['teamlid' => $team]);
    }

    public function update(Request $request, Teamlid $team)
    {
        $data = $this->gevalideerd($request);

        if ($request->hasFile('foto') && $team->foto) {
            Storage::disk('public')->delete($team->foto);
        }

        $team->update($this->metFoto($request, $data));

        return redirect()
            ->route('admin.team.index')
            ->with('succes', 'Opdrachtgever bijgewerkt.');
    }

    public function destroy(Teamlid $team)
    {
        if ($team->foto) {
            Storage::disk('public')->delete($team->foto);
        }

        $team->delete();

        return redirect()
            ->route('admin.team.index')
            ->with('succes', 'Opdrachtgever verwijderd.');
    }

    private function gevalideerd(Request $request): array
    {
        return $request->validate([
            'naam' => ['required', 'string', 'max:100'],
            'functie' => ['nullable', 'string', 'max:100'],
            'biografie' => ['nullable', 'string', 'max:2000'],
            'volgorde' => ['nullable', 'integer', 'min:0', 'max:999'],
            'actief' => ['required', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    /** Slaat een nieuwe foto op en zet het pad in de gegevens; zonder upload blijft de oude foto staan. */
    private function metFoto(Request $request, array $data): array
    {
        unset($data['foto']);

        $data['volgorde'] = (int) ($data['volgorde'] ?? 0);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('team', 'public');
        }

        return $data;
    }
}
