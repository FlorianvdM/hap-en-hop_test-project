{{-- Het icoon in het tabblad. Staat in één component, zodat elke pagina hetzelfde icoon gebruikt.
     De ?v=2 zorgt dat de browser het nieuwe icoon ophaalt in plaats van een oud, onthouden icoon. --}}
<link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=4">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">