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
@endsection