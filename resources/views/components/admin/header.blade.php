<header class="site-header">
    <div class="header-inner">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Hap &amp; Hop" height="50">
        </a>

        <details class="gebruikersmenu">
            <summary>
                <x-icon name="user" :size="20" />
                <span>{{ auth()->user()->name }}</span>
                <x-icon name="chevron-down" :size="18" />
            </summary>
            <div class="menu">
                <a href="{{ route('admin.instellingen') }}">Instellingen</a>
                <a href="{{ route('home') }}">Naar de website</a>
                <a href="{{ route('logout.confirm') }}">Uitloggen</a>
            </div>
        </details>
    </div>
</header>