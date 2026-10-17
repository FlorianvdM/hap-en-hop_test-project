<?php

namespace App\Support;

class Onderhoud
{
    /** Staat de onderhoudsmodus aan? */
    public static function aan(): bool
    {
        return is_file(self::bestand());
    }

    /** Zet de onderhoudsmodus aan of uit. */
    public static function zet(bool $aan): void
    {
        if ($aan) {
            file_put_contents(self::bestand(), now()->toDateTimeString());
        } elseif (is_file(self::bestand())) {
            unlink(self::bestand());
        }
    }

    /**
     * Het bestand dat bijhoudt of de onderhoudsmodus aan staat. Het staat in een map die Git negeert,
     * zodat de instelling nooit per ongeluk op GitHub of op de echte server terechtkomt.
     * Tijdens het testen gebruiken we een eigen bestand, zodat een aangezette modus de tests niet verstoort.
     */
    private static function bestand(): string
    {
        $naam = app()->environment('testing') ? 'onderhoud-test.flag' : 'onderhoud.flag';

        return storage_path('app/private/' . $naam);
    }
}