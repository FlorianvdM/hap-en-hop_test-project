# Hap & Hop – projectinstructies voor Claude Code

Webshop voor de cateraar Hap & Hop (Houten en omgeving): pakketten bestellen, winkelwagen, beheerderspaneel.
Schoolproject van Flo (MBO Utrecht, BE-Backend).

## Stack
- Laravel 13, PHP 8.4 of hoger, MySQL (InnoDB), Blade-views.
- Geen Tailwind of Vite: eigen CSS in `public/css/` (`style.css`, `extra.css`, `beheer.css`).
- Sessies, cache en wachtrij gebruiken de database.
- Mail lokaal via Mailtrap (gratis sandbox). Instellingen staan in `config/hapenhop.php` en `.env`.

## Commando's
- Server starten: `php artisan serve`
- Tests: `php artisan test` (draait op SQLite in het geheugen en raakt de echte database niet aan)
- Database opnieuw opbouwen (alleen lokaal, wist alles): `php artisan migrate:fresh --seed`
- Caches leegmaken na wijzigingen: `php artisan optimize:clear`
- Mails op de achtergrond (alleen met HAPHOP_MAIL_WACHTRIJ=true): `php artisan queue:work --tries=3`

## Werkafspraken
- Schrijf antwoorden, commentaar en teksten op de website in het Nederlands, met "je" als aanspreekvorm.
- Lees of wijzig `.env` nooit en zet geen wachtwoorden of sleutels in code, chat of commits.
- Draai `php artisan test` na elke wijziging en herstel fouten voordat je klaar meldt.
- Vraag eerst toestemming voor: migraties op een bestaande database, `migrate:fresh`, bestanden verwijderen,
  `composer require` of `composer update`, en elke `git commit` of `git push`.
- Houd wijzigingen klein en leg kort uit wat je veranderde en waarom.

## Structuur
- Controllers: `app/Http/Controllers` (beheer: `Admin*Controller`). Modellen: `app/Models`.
- Hulpklassen: `app/Support` (`Winkelwagen`, `Onderhoud`).
- Views: `resources/views`. Herbruikbare onderdelen in `resources/views/components`
  (`<x-header />`, `<x-icon />`, `<x-favicon />`, ...). Layouts: `layouts/app` (publiek) en `layouts/admin`.
- Verwijderknoppen in het beheer gebruiken `data-bevestig="..."` op het formulier (eigen pop-up).
  Zet daar alleen vaste tekst in, nooit klantgegevens.
- Meldingen: `->with('fout', '...')` komt in de pop-up, `->with('succes', '...')` verdwijnt na 8 seconden.

## Bedrijfsregels (niet veranderen zonder overleg)
- Een bestelling bestaat uit regels (`bestelregels`) met de prijs van het moment van bestellen.
- Bestelling verwijderen: status `verzonden` wordt soft deleted en telt mee in de omzet (maand van verwijderen).
  Andere statussen worden definitief verwijderd en tellen nooit mee.
- Een beheerder kan zichzelf of een andere beheerder niet verwijderen (eerst de rol wijzigen).
- Een pakket met bestelregels kan niet worden verwijderd, alleen verborgen.
  Een bericht verwijderen kan pas nadat het gelezen is.
- Onderhoudsmodus (Instellingen): bezoekers krijgen een 404, beheerders en `/login` blijven bereikbaar.
  De schakelaar staat in `storage/app/private/onderhoud.flag` (niet in Git).
- Tabellen moeten InnoDB zijn (afgedwongen in `config/database.php`).

## Nog te doen
- Online betalen met Mollie (iDEAL | Wero): wacht tot Hap & Hop een geregistreerd bedrijf (KvK) is.
  Verzin nooit bedrijfsgegevens.
- Online zetten (hosting), als allerlaatste stap.

## Herinnering voor Flo vóór het online zetten
Herinner Flo hier actief aan zodra het over hosting of livegang gaat:
- `.env`: `APP_DEBUG=false`, `APP_ENV=production`, eigen databasegebruiker met sterk wachtwoord,
  echte `APP_URL` en een echt afzenderadres.
- `HAPHOP_MAIL_PAUZE` weghalen en de wachtrij (`queue:work`) op de server regelen.
- Het standaard beheerwachtwoord (`admin@hapenhop.nl`) wijzigen.
- `.env` staat nooit op GitHub. De onderhoudsmodus staat uit.
- Privacyverklaring nalopen (o.a. dieetwensen zoals halal, en Mollie).