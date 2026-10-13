<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hap &amp; Hop')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('home') }}" class="logo">Hap &amp; Hop</a>

        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'actief' : '' }}">Home</a></li>
            <li><a href="{{ route('pakketten.index') }}" class="{{ request()->routeIs('pakketten.*', 'bestellen.*') ? 'actief' : '' }}">Pakketten</a></li>
            <li><a href="{{ route('info') }}" class="{{ request()->routeIs('info') ? 'actief' : '' }}">Info</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'actief' : '' }}">Contact</a></li>

            @auth
                @if (auth()->user()->is_admin)
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'actief' : '' }}">Beheer</a></li>
                @endif
                <li class="gebruiker">{{ auth()->user()->name }}</li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-knop">Uitloggen</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'actief' : '' }}">Inloggen</a></li>
                <li><a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'actief' : '' }}">Registreren</a></li>
            @endauth
        </ul>
    </nav>

    <main class="container">
        @if (session('succes'))
            <div class="melding succes">{{ session('succes') }}</div>
        @endif

        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Hap &amp; Hop &middot; Houten &amp; omgeving
    </footer>
</body>
</html>