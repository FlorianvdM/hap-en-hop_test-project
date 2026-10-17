<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Even geduld | Hap &amp; Hop</title>
    <x-favicon />
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #1c2b22; background: #fff; }
        header { background: #175432; padding: 12px 24px; }
        header img { height: 56px; display: block; }
        main { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 48px 24px; }
        .code { font-size: 4rem; font-weight: 800; line-height: 1; margin: 0 0 8px; color: #175432; }
        h1 { font-size: 2rem; margin: 0 0 12px; }
        p { max-width: 480px; margin: 0 0 24px; color: #6b7280; line-height: 1.5; }
        a.knop { display: inline-block; background: #175432; color: #fff; border-radius: 8px; padding: 12px 20px; font-weight: 600; text-decoration: none; }
        footer { background: #f3f4f6; color: #6b7280; text-align: center; padding: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header><img src="{{ asset('images/logo.png') }}" alt="Hap &amp; Hop"></header>
    <main>
        <p class="code">503</p>
        <h1>We zijn zo terug</h1>
        <p>
            Er is op dit moment een technische storing, waardoor de website tijdelijk niet werkt.
            Probeer het over een paar minuten nog eens.
        </p>
        <a class="knop" href="javascript:location.reload()">Opnieuw proberen</a>
    </main>
    <footer>&copy; Hap &amp; Hop</footer>
</body>
</html>