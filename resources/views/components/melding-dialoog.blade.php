<dialog id="melding-dialoog" class="melding-dialoog"
        aria-labelledby="melding-titel"
        data-fout="{{ session('fout') }}">
    <form method="dialog">
        <h2 id="melding-titel"></h2>
        <p id="melding-tekst"></p>

        <div class="acties">
            <button type="submit" value="bevestigd" id="melding-bevestig" class="knop"></button>
            <button type="submit" value="geannuleerd" id="melding-annuleer" class="knop knop-licht">Annuleren</button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        const dialoog = document.getElementById('melding-dialoog');
        const titel = document.getElementById('melding-titel');
        const tekst = document.getElementById('melding-tekst');
        const bevestigKnop = document.getElementById('melding-bevestig');
        const annuleerKnop = document.getElementById('melding-annuleer');
        let bijBevestigen = null;

        function toon(opties, actie) {
            titel.textContent = opties.kop;
            tekst.textContent = opties.bericht;
            bevestigKnop.textContent = opties.knop;
            bevestigKnop.classList.toggle('knop-gevaar', Boolean(opties.gevaar));
            annuleerKnop.hidden = Boolean(opties.alleenInfo);
            bijBevestigen = actie || null;
            dialoog.returnValue = '';
            dialoog.showModal();
        }

        dialoog.addEventListener('close', function () {
            if (dialoog.returnValue === 'bevestigd' && bijBevestigen) {
                bijBevestigen();
            }
            bijBevestigen = null;
        });

        // Elk formulier met data-bevestig vraagt eerst om bevestiging in deze pop-up
        document.addEventListener('submit', function (event) {
            const formulier = event.target;

            if (!formulier.dataset || !formulier.dataset.bevestig) {
                return;
            }

            event.preventDefault();

            toon({
                kop: formulier.dataset.bevestigTitel || 'Weet je het zeker?',
                bericht: formulier.dataset.bevestig,
                knop: formulier.dataset.bevestigKnop || 'Verwijderen',
                gevaar: true,
            }, function () {
                formulier.submit();
            });
        }, true);

        // Foutmeldingen van de server komen in dezelfde pop-up
        if (dialoog.dataset.fout) {
            toon({
                kop: 'Dit kan niet',
                bericht: dialoog.dataset.fout,
                knop: 'Sluiten',
                alleenInfo: true,
            });
        }
    })();
</script>