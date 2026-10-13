@extends(auth()->user()->is_admin ? 'layouts.admin' : 'layouts.app')

@section('title', 'Uitloggen | Hap &amp; Hop')
@section('zonder_zijbalk', 'ja')

@section('content')
    <div class="uitloggen">
        <x-icon name="log-out" :size="48" />
        <h1>Uitloggen</h1>
        <p class="muted">Weet je zeker dat je wilt uitloggen?</p>

        <div class="knoppen">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="knop">Uitloggen</button>
            </form>

            <a class="knop knop-licht" href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('home') }}">
                Annuleren
            </a>
        </div>
    </div>
@endsection