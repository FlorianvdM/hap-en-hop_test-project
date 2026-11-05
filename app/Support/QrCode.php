<?php

namespace App\Support;

use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode as Generator;
use chillerlan\QRCode\QROptions;

/**
 * De QR-code voor flyers en de toonbank: leidt meteen naar de bestelpagina.
 * SVG, omdat die scherp blijft bij elk formaat en geen GD-extensie nodig heeft.
 */
class QrCode
{
    /** Het adres in de QR-code: HAPHOP_QR_URL uit .env, anders de pakketpagina op APP_URL. */
    public static function url(): string
    {
        return config('hapenhop.qr_url') ?: route('pakketten.index');
    }

    /** Wijst de QR-code nog naar de eigen computer? Dan werkt hij niet op de telefoon van een klant. */
    public static function isLokaal(): bool
    {
        $host = parse_url(self::url(), PHP_URL_HOST);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with((string) $host, '.test');
    }

    public static function svg(): string
    {
        $opties = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
        ]);

        return (new Generator($opties))->render(self::url());
    }
}
