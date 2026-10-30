# Hap & Hop live zetten (TransIP webhosting)

Deze checklist is geschreven voor shared hosting met SSH en Composer, zoals bij TransIP. Bij een andere host zijn
de stappen hetzelfde, alleen de plek van de instellingen in het controlepaneel verschilt.

## 0. Voorbereiding (nog niet op de server)
- [ ] `php artisan test` is groen en alles staat gepusht op GitHub (`git status` is schoon)
- [ ] Domeinnaam geregistreerd, met een beheerder van Hap & Hop als houder
- [ ] Webhostingpakket besteld, PHP staat op **8.4** (controlepaneel > Shared Hosting > je domein > Geavanceerd > PHP-instellingen)
- [ ] SSL-certificaat staat aan en is actief
- [ ] MySQL-database en databasegebruiker gemaakt (noteer naam, gebruiker, wachtwoord en host)
- [ ] Mailbox gemaakt voor het afzenderadres (bijvoorbeeld bestellingen@jouwdomein.nl)
- [ ] SSH-gegevens bekend (server en gebruikersnaam)
- [ ] Van de beheerders: pakketten, prijzen, foto's, logo, teksten en het KvK-nummer (als dat er is)

## 1. Code op de server zetten
Log in via SSH (de gegevens staan in het controlepaneel):

```bash
ssh gebruikersnaam@servernaam
php -v
```

`php -v` moet **PHP 8.4** tonen. Toont het een lagere versie, vraag dan de support van de host hoe je de
PHP-versie voor SSH instelt. Verder gaan heeft geen zin: de packages vragen minimaal PHP 8.4.1.

Haal de code op. Is de GitHub-repo openbaar:

```bash
git clone https://github.com/FlorianvdM/hap-en-hop_test-project.git hap-en-hop
cd hap-en-hop
```

Is de repo privé, of werkt `git clone` niet? Upload de bestanden dan via SFTP, **zonder** `vendor`, `node_modules` en `.env`.
Zet nooit een persoonlijk token in een chat of in een bestand.

## 2. Webmap instellen (DocumentRoot)
Stel in het controlepaneel de **DocumentRoot** van je domein in op de map `public` van het project,
bijvoorbeeld `/hap-en-hop/public`. Zoek in de TransIP-kennisbank op "The DocumentRoot of your website".
Doe je dit niet, dan staan `.env` en je code publiek op internet. Dat mag nooit.

## 3. Installeren
In de projectmap op de server:

```bash
composer install --no-dev --optimize-autoloader
cp .env.production.example .env
nano .env
php artisan key:generate
```

Vul in `.env` alles in wat tussen `[haken]` staat. Let op:
- `APP_URL` begint met `https://`
- `APP_DEBUG=false` en `APP_ENV=production` (pas nadat SSL actief is)
- eigen databasegebruiker met sterk wachtwoord
- echt afzenderadres bij `MAIL_FROM_ADDRESS` en de SMTP-gegevens van de host
- `HAPHOP_MAIL_PAUZE` staat er niet in (goed zo)
- `HAPHOP_KVK` leeg laten zolang er geen KvK-nummer is

## 4. Database en beheerder
```bash
php artisan migrate --force
php artisan db:seed --force
```

`db:seed` maakt op de echte website alleen de eerste beheerder aan (met het wachtwoord uit `.env`) en laadt geen
voorbeeldpakketten. Daarna:

```bash
nano .env
```

Haal de regel `HAPHOP_ADMIN_WACHTWOORD` weg. Wijzig daarna in het beheerpaneel (Gebruikers) het wachtwoord.

**Gebruik op de echte website nooit `migrate:fresh`.** Dat wist alle bestellingen.

## 5. Bestanden en rechten
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

Lukt `storage:link` niet (sommige hosts staan symlinks niet toe), vraag dan de support hoe je een link naar
`storage/app/public` maakt. Uploads van foto's komen daar terecht.

## 6. Versnellen
```bash
php artisan config:cache
php artisan view:cache
```

Doe **niet** `php artisan route:cache`: de 404-route in `routes/web.php` is een closure en die kan niet in de cache.

## 7. Controleren
- [ ] De website opent op `https://jouwdomein.nl` met een slotje, zonder foutmelding
- [ ] `/bestaat-niet` toont de eigen 404-pagina
- [ ] Inloggen als beheerder werkt, wachtwoord is gewijzigd
- [ ] Pakketten, broodjes, "Wie zijn wij" en reviews zijn ingevuld, foto's zijn geüpload en zichtbaar
- [ ] Testbestelling plaatsen: de bevestigingsmail en de melding voor Hap & Hop komen aan (kijk ook in de spam)
- [ ] De testbestelling in het beheerpaneel verwijderen (voorraad komt terug)
- [ ] Telefoonnummer, e-mailadres en sociale iconen kloppen en de links openen de juiste pagina's
- [ ] Alles ook bekeken op een telefoon
- [ ] Onderhoudsmodus staat uit
- [ ] Back-ups staan aan in het controlepaneel
- [ ] `.env` staat nergens op GitHub (`git log --all -- .env` geeft niets)

Komen mails niet aan? Controleer de SMTP-gegevens in `.env` en de SPF/DKIM-instellingen van het domein.
Zoek in de kennisbank van de host op "website is not sending mail".

## 8. Een update uitrollen
Lokaal: testen, committen, pushen. Daarna op de server:

```bash
cd hap-en-hop
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

## 9. Als er iets misgaat
| Probleem | Wat te doen |
| --- | --- |
| Witte pagina of foutmelding 500 | Lees de laatste regels van `storage/logs/laravel.log`. Zet `APP_DEBUG` niet aan op de echte website. |
| Alle pagina's geven 404 | Controleer de DocumentRoot (stap 2) en of `.htaccess` in `public` staat. |
| "Class not found" of andere ontbrekende code | Draai `composer install --no-dev --optimize-autoloader` opnieuw. |
| Wijziging in `.env` werkt niet | Draai `php artisan config:cache` opnieuw. |
| Foto's verschijnen niet | Controleer `php artisan storage:link` en de rechten van `storage`. |
| Database-fout | Controleer de databasegegevens in `.env` en draai `php artisan config:cache` opnieuw. |

## 10. Later
- Online betalen (Mollie, iDEAL | Wero) kan pas als Hap & Hop in het KvK-handelsregister staat.
- AI-functies (pakketadvies en conceptantwoorden) staan uit zolang er geen `ANTHROPIC_API_KEY` is.
  Maak ze alleen aan na een aparte afspraak over kosten en privacy.