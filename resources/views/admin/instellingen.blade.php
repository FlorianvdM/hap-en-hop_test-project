@extends('layouts.admin')

@section('title', 'Instellingen | Hap &amp; Hop')

@section('content')
    <h1>Beheerder</h1>
    <p class="muted" style="margin-bottom: 20px;">
        Je bent ingelogd als beheerder. Hier heb je toegang tot alle functies.
    </p>

    <div class="profiel-kaart">
        <div class="avatar">
            <x-icon name="user" :size="28" />
        </div>
        <div>
            <h2>{{ auth()->user()->name }}</h2>
            <p class="muted">Volledige toegang tot het systeem.</p>
            <a class="knop" href="#profiel">Profiel bekijken</a>
        </div>
    </div>

    <section id="profiel" class="profiel-details">
        <h2>Profiel</h2>
        <dl>
            <dt>Naam</dt>
            <dd>{{ auth()->user()->name }}</dd>

            <dt>E-mailadres</dt>
            <dd>{{ auth()->user()->email }}</dd>

            <dt>Account aangemaakt</dt>
            <dd>{{ auth()->user()->created_at->format('d-m-Y') }}</dd>
        </dl>
    </section>

    <section class="profiel-details" style="max-width: 760px;">
        <h2>Alle beheerders</h2>
        <p class="muted" style="margin-bottom: 12px;">
            Deze accounts hebben volledige toegang tot het beheer. De rol van een account wijzig je onder
            <a href="{{ route('admin.gebruikers.index') }}">Gebruikers</a>.
        </p>

        <div class="tabel-wrap" style="margin-bottom: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>E-mailadres</th>
                        <th>Aangemaakt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($beheerders as $beheerder)
                        <tr>
                            <td>
                                {{ $beheerder->name }}
                                @if ($beheerder->is(auth()->user()))
                                    <span class="muted">(jij)</span>
                                @endif
                            </td>
                            <td>{{ $beheerder->email }}</td>
                            <td>{{ $beheerder->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="profiel-details" style="max-width: 760px;">
        <h2>Onderhoudsmodus</h2>
        <p class="muted" style="margin-bottom: 12px;">
            Staat de onderhoudsmodus aan, dan zien alle bezoekers een 404-pagina, op elke pagina van de website.
            Jij en andere ingelogde beheerders blijven de website gewoon zien. De inlogpagina blijft bereikbaar,
            zodat je jezelf nooit buitensluit.
        </p>

        <p style="margin-bottom: 12px;">
            Status:
            @if ($onderhoud)
                <span class="status status-nieuw">Onderhoudsmodus aan</span>
            @else
                <span class="status status-zichtbaar">Website bereikbaar</span>
            @endif
        </p>

        <form method="POST" action="{{ route('admin.onderhoud') }}"
              @unless ($onderhoud)
                  data-bevestig-titel="Onderhoudsmodus aanzetten?"
                  data-bevestig="Alle bezoekers zien dan een 404-pagina. Jij blijft als beheerder toegang houden tot de website."
                  data-bevestig-knop="Aanzetten"
              @endunless>
            @csrf
            <button type="submit" class="knop">
                {{ $onderhoud ? 'Onderhoudsmodus uitzetten' : 'Onderhoudsmodus aanzetten' }}
            </button>
        </form>
    </section>
@endsection