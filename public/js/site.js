// Kleine hulpjes voor de hele website van Hap & Hop
(function () {
    // 1. Groene meldingen (bijvoorbeeld "Bedankt voor je bestelling") verdwijnen na 4 seconden
    document.querySelectorAll('[data-verdwijnt]').forEach(function (melding) {
        var wachttijd = parseInt(melding.getAttribute('data-verdwijnt'), 10) || 4000;

        setTimeout(function () {
            melding.style.transition = 'opacity 0.5s ease';
            melding.style.opacity = '0';

            setTimeout(function () {
                melding.remove();
            }, 500);
        }, wachttijd);
    });

    // 2. Formulieren met data-laden tonen direct dat ze bezig zijn en kunnen niet twee keer worden verstuurd
    document.addEventListener('submit', function (event) {
        var formulier = event.target;
        var tekst = formulier.getAttribute ? formulier.getAttribute('data-laden') : null;

        if (!tekst || event.defaultPrevented) {
            return;
        }

        var knop = formulier.querySelector('button[type="submit"]');

        if (!knop) {
            return;
        }

        // Pas na het versturen aanpassen, anders wordt het formulier niet meegestuurd
        setTimeout(function () {
            knop.dataset.origineel = knop.textContent;
            knop.textContent = tekst;
            knop.disabled = true;
        }, 0);
    });

    // 3. Velden met data-auto-verstuur sturen hun formulier zelf op zodra de waarde verandert
    document.addEventListener('change', function (event) {
        var veld = event.target;

        if (!veld.hasAttribute || !veld.hasAttribute('data-auto-verstuur') || !veld.form) {
            return;
        }

        if (veld.checkValidity()) {
            veld.form.submit();
        } else {
            veld.reportValidity();
        }
    });

    // Blokje onder de winkelwagenknop na "In winkelwagen": fadet in, blijft 5 seconden staan en fadet weer uit.
    // Met de muis erop of met het toetsenbord erin blijft het staan, zodat je rustig op een knop kunt klikken.
    var wagenMelding = document.getElementById('wagen-melding');

    if (wagenMelding) {
        var wagenTimer = null;

        var verberg = function () {
            wagenMelding.classList.remove('zichtbaar');
            setTimeout(function () { wagenMelding.remove(); }, 400);
        };
        var start = function (ms) {
            clearTimeout(wagenTimer);
            wagenTimer = setTimeout(verberg, ms);
        };

        requestAnimationFrame(function () {
            requestAnimationFrame(function () { wagenMelding.classList.add('zichtbaar'); });
        });
        start(5000);

        wagenMelding.addEventListener('mouseenter', function () { clearTimeout(wagenTimer); });
        wagenMelding.addEventListener('focusin', function () { clearTimeout(wagenTimer); });
        wagenMelding.addEventListener('mouseleave', function () { start(2000); });
        wagenMelding.querySelector('.wagen-melding-sluit').addEventListener('click', verberg);
    }

    // 4. Het adresblok bij het bestelformulier staat alleen open als de klant bezorging kiest.
    //    Zodra het adres is ingevuld, vragen we de bezorgkosten op en passen we het totaal aan.
    var adresBlok = document.getElementById('bezorgadres');

    if (adresBlok) {
        var info = document.getElementById('bezorg-info');
        var kostenRegel = document.getElementById('bezorgkosten-regel');
        var kostenBedrag = document.getElementById('bezorgkosten-bedrag');
        var totaal = document.getElementById('besteltotaal');
        var subtotaal = parseFloat(totaal.dataset.subtotaal);
        var bezorgkosten = null; // null = nog niet bekend

        // 12.5 wordt "€ 12,50"
        var euro = function (bedrag) {
            return '€ ' + bedrag.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        var gekozenBezorgen = function () {
            var gekozen = document.querySelector('input[name="levering"]:checked');
            return gekozen && gekozen.value === 'bezorgen';
        };

        // Bezorgregel en totaal in het overzicht bijwerken
        var werkOverzichtBij = function () {
            var bezorgen = gekozenBezorgen();

            kostenRegel.hidden = !bezorgen;
            kostenBedrag.textContent = bezorgkosten === null ? 'nog niet bekend' : euro(bezorgkosten);
            totaal.textContent = euro(subtotaal + (bezorgen && bezorgkosten !== null ? bezorgkosten : 0));
        };

        var meldingTonen = function (tekst, isFout) {
            info.textContent = tekst;
            info.className = isFout ? 'fout' : 'notitie'; // rood bij een fout, anders grijs
        };

        // Vraagt de bezorgkosten op bij de website (route /bezorgkosten)
        var berekenKosten = function () {
            var adres = {
                straat: document.getElementById('straat').value.trim(),
                postcode: document.getElementById('postcode').value.trim(),
                plaats: document.getElementById('plaats').value.trim()
            };

            bezorgkosten = null;

            if (!adres.straat || !adres.postcode || !adres.plaats) {
                meldingTonen('Vul je adres in, dan zie je meteen de bezorgkosten.', false);
                werkOverzichtBij();
                return;
            }

            meldingTonen('Bezorgkosten berekenen...', false);

            fetch(adresBlok.dataset.url + '?' + new URLSearchParams(adres), { headers: { Accept: 'application/json' } })
                .then(function (antwoord) {
                    return antwoord.json().then(function (data) {
                        if (antwoord.ok) {
                            bezorgkosten = data.kosten;
                            meldingTonen('Afstand ongeveer ' + data.km + ' km. Bezorgkosten: ' + data.tekst + '.', false);
                        } else {
                            meldingTonen(data.fout || 'Controleer je adres.', true);
                        }
                    });
                })
                .catch(function () {
                    meldingTonen('De bezorgkosten konden niet worden berekend. Je ziet ze na het bestellen in de bevestiging.', true);
                })
                .finally(werkOverzichtBij);
        };

        var toonAdres = function () {
            var bezorgen = gekozenBezorgen();

            adresBlok.hidden = !bezorgen;
            adresBlok.querySelectorAll('input').forEach(function (veld) {
                veld.required = bezorgen;
            });

            werkOverzichtBij();
        };

        document.querySelectorAll('input[name="levering"]').forEach(function (keuze) {
            keuze.addEventListener('change', toonAdres);
        });

        // "change" gaat af als de klant een veld verlaat, dus niet bij elke letter
        ['straat', 'postcode', 'plaats'].forEach(function (id) {
            document.getElementById(id).addEventListener('change', berekenKosten);
        });

        toonAdres();

        // Komt de klant terug na een foutmelding en staat het adres er nog, dan meteen opnieuw berekenen
        if (gekozenBezorgen()) {
            berekenKosten();
        }
    }

    // Komt de bezoeker met de terugknop terug op de pagina, dan werkt de knop weer
    window.addEventListener('pageshow', function (event) {
        if (!event.persisted) {
            return;
        }

        document.querySelectorAll('button[data-origineel]').forEach(function (knop) {
            knop.textContent = knop.dataset.origineel;
            knop.disabled = false;
        });
    });

    // 4. Uitklapmenu "Over ons": klikken (of tikken) klapt open, Escape of buiten klikken sluit
    document.querySelectorAll('.menu-groep').forEach(function (groep) {
        var knop = groep.querySelector('.menu-knop');

        function zet(open) {
            groep.classList.toggle('open', open);
            knop.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        knop.addEventListener('click', function () {
            zet(!groep.classList.contains('open'));
        });

        document.addEventListener('click', function (event) {
            if (!groep.contains(event.target)) {
                zet(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && groep.classList.contains('open')) {
                zet(false);
                knop.focus();
            }
        });
    });

    // 5. Menuknop op een telefoon: klapt het hoofdmenu open en dicht
    var kop = document.getElementById('site-header');
    var menuKnop = document.querySelector('.menu-knop-mobiel');

    if (kop && menuKnop) {
        menuKnop.addEventListener('click', function () {
            var open = kop.classList.toggle('menu-open');

            menuKnop.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuKnop.setAttribute('aria-label', open ? 'Menu sluiten' : 'Menu openen');
        });
    }

    // 3. Offline-pagina: als de website niet bereikbaar is, laat de browser een eigen melding zien
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
})();