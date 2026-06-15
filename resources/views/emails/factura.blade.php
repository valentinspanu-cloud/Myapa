<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factură Aquaserv Tulcea</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:Arial,sans-serif;font-size:14px;color:#333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">

    {{-- Logo --}}
    <tr>
        <td align="center" style="padding:20px;border-bottom:1px solid #e8edf2;">
            <img src="{{ config('app.url') }}/images/logo-aquaserv.png" alt="Aquaserv Tulcea" style="height:60px;">
        </td>
    </tr>

    {{-- Titlu --}}
    <tr>
        <td style="background:#1a5276;padding:18px 24px;text-align:center;">
            <div style="color:#fff;font-size:18px;font-weight:bold;margin-bottom:6px;">
                Stimate client(ă) {{ strtoupper($numeClient) }}!
            </div>
            <div style="color:#fff;font-size:14px;opacity:0.9;">
                Am emis factura Aquaserv pentru luna {{ $luna }}
            </div>
        </td>
    </tr>

    {{-- Rând 1: Cod client, Nr factură, Emisă la --}}
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="33%" style="padding:16px 12px;text-align:center;border:1px solid #e8edf2;">
                        <div style="font-size:24px;margin-bottom:6px;">👤</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Cod Client</div>
                        <div style="font-size:15px;font-weight:bold;color:#1a5276;">{{ $codClient }}</div>
                    </td>
                    <td width="33%" style="padding:16px 12px;text-align:center;border:1px solid #e8edf2;">
                        <div style="font-size:24px;margin-bottom:6px;">📄</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Nr. Factură</div>
                        <div style="font-size:15px;font-weight:bold;color:#1a5276;">{{ $nrFactura }}</div>
                    </td>
                    <td width="33%" style="padding:16px 12px;text-align:center;border:1px solid #e8edf2;">
                        <div style="font-size:24px;margin-bottom:6px;">✏️</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Emisă La</div>
                        <div style="font-size:15px;font-weight:bold;color:#1a5276;">{{ $dataEmitere }}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Rând 2: Scadență, Sold --}}
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" style="padding:16px 12px;text-align:center;border:1px solid #e8edf2;">
                        <div style="font-size:24px;margin-bottom:6px;">⏰</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Scadență</div>
                        <div style="font-size:15px;font-weight:bold;color:#1a5276;">{{ $scadenta }}</div>
                    </td>
                    <td width="50%" style="padding:16px 12px;text-align:center;border:1px solid #e8edf2;">
                        <div style="font-size:24px;margin-bottom:6px;">💳</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Sold Neachitat La {{ $dataEmitere }}</div>
                        <div style="font-size:20px;font-weight:bold;color:#e74c3c;">{{ $sold }} LEI</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- CTA --}}
    <tr>
        <td style="text-align:center;padding:20px 24px;">
            <a href="{{ config('app.url') }}" style="display:inline-block;background:#1a5276;color:#fff;text-decoration:none;padding:13px 32px;border-radius:5px;font-size:15px;font-weight:bold;">
                Pentru autentificare în portalul <strong>MyAPA</strong> apasă aici!
            </a>
        </td>
    </tr>

    {{-- Text legal --}}
    <tr>
        <td style="padding:0 24px 16px;font-size:13px;color:#555;line-height:1.6;">
            <p>Neachitarea facturii în termen de 30 de zile de la data scadenței atrage penalități de întârziere, începând cu prima zi după data scadenței înscrisă în factură.</p>
            <br>
            <p>S.C. AQUASERV S.A. Tulcea este operator de date cu caracter personal și operează sub incidența Regulamentului 679/27.04.2016 privind protecția persoanelor fizice în ceea ce privește prelucrarea datelor cu caracter personal și libera circulație a acestor date.</p>
        </td>
    </tr>

    {{-- Atașament --}}
    <tr>
        <td style="padding:0 24px 16px;">
            <div style="background:#f8f9fa;border:1px dashed #ccc;border-radius:5px;padding:12px 16px;font-size:13px;color:#555;text-align:center;">
                📎 Regăsiți în atașament factura în format .pdf .
            </div>
        </td>
    </tr>

    {{-- Mesaj auto --}}
    <tr>
        <td style="background:#f8f9fa;border-top:1px solid #e8edf2;padding:14px 24px;text-align:center;font-size:12px;color:#888;font-style:italic;">
            Acest mesaj a fost generat automat. Vă rugăm să nu dați reply.<br>
            Vă mulțumim pentru cooperare și înțelegere!
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#1a5276;color:#fff;text-align:center;padding:16px;font-size:14px;font-weight:bold;">
            Echipa Aquaserv – Mereu în serviciul dumneavoastră
        </td>
    </tr>

</table>
</td></tr>
</table>

</body>
</html>
