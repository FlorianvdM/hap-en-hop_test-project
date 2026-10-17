<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titel', 'Hap & Hop')</title>
</head>
<body style="margin: 0; padding: 0; background: #f3f4f6; font-family: Arial, Helvetica, sans-serif; color: #1c2b22;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f3f4f6; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                       style="max-width: 600px; width: 100%; background: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="background: #175432; padding: 14px 28px; color: #d9b45a; font-size: 22px; font-weight: bold;">
                            @if (isset($message) && is_file(public_path('images/logo-mail.png')))
                                <img src="{{ $message->embed(public_path('images/logo-mail.png')) }}" alt="Hap &amp; Hop" height="80"
                                     style="display: block; height: 80px; width: auto; border: 0;">
                            @else
                                Hap &amp; Hop
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px; font-size: 16px; line-height: 1.5;">
                            @yield('inhoud')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 28px; background: #f3f4f6; color: #6b7280; font-size: 13px;">
                            Hap &amp; Hop &middot; Catering, broodjes en pakketten in Houten en omgeving<br>
                            Vragen? Mail naar {{ config('hapenhop.email') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>