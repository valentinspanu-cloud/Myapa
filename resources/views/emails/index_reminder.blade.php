@extends('layouts.email')
@section('title', 'Transmitere index contor')
@section('preHeader', 'Perioada de transmitere index: 10-20 ' . now()->locale('ro')->isoFormat('MMMM YYYY'))
@section('content')
<tr>
    <td style="padding:0 0 20px 0;">
        <h2 style="font-size:18px;font-weight:600;color:#0C2340;margin-bottom:16px;">
            Stimate client(a) <span style="color:#c0392b;">{{ strtoupper($user->name) }}</span>
        </h2>
        <p style="font-size:14px;color:#374151;line-height:1.7;margin-bottom:16px;">
            Va reamintim ca in intervalul <strong>10 - 20 {{ now()->locale('ro')->isoFormat('MMMM YYYY') }}</strong>
            puteti transmite indexul autocitit al contorului dumneavoastra de apa.
        </p>
        <p style="font-size:14px;font-weight:600;color:#0C2340;margin-bottom:12px;">Cum comunic indexul?</p>
        <table style="width:100%;margin-bottom:20px;">
            <tr>
                <td style="padding:8px 0;font-size:14px;color:#374151;"><strong>Prin portalul MyAPA:</strong></td>
                <td style="padding:8px 0;text-align:right;">
                    <a href="https://my.aquaservtulcea.ro" style="background:#0C2340;color:#ffffff;padding:8px 18px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600;">Acceseaza portalul</a>
                </td>
            </tr>
            <tr><td colspan="2"><hr style="border:none;border-top:1px solid #f3f4f6;margin:4px 0;"></td></tr>
            <tr>
                <td colspan="2" style="padding:8px 0;font-size:14px;color:#374151;">
                    Telefonic la <strong>+40 0340 131 111</strong> folosind cod client <strong>{{ $user->codes[0]['client_code'] ?? '-' }}</strong>
                </td>
            </tr>
            <tr><td colspan="2"><hr style="border:none;border-top:1px solid #f3f4f6;margin:4px 0;"></td></tr>
            <tr>
                <td colspan="2" style="padding:8px 0;font-size:14px;color:#374151;">La sediul fiecarui Centru Operational Aquaserv</td>
            </tr>
            <tr><td colspan="2"><hr style="border:none;border-top:1px solid #f3f4f6;margin:4px 0;"></td></tr>
            <tr>
                <td colspan="2" style="padding:8px 0;font-size:14px;color:#374151;">La cititorul-incasator arondat zonei</td>
            </tr>
        </table>
        <p style="font-size:13px;color:#6B7280;line-height:1.6;margin-bottom:20px;">
            Transmiterea indexului autocitit nu exclude citirea periodica realizata de catre
            reprezentantul Aquaserv si obligatia dumneavoastra de a permite accesul.
        </p>
        <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:16px;text-align:center;margin-bottom:20px;">
            <p style="font-size:12px;color:#6B7280;margin-bottom:6px;">Totodata va informam ca figurati in evidentele noastre cu:</p>
            <p style="font-size:12px;font-weight:600;color:#c0392b;margin-bottom:4px;">SOLD NEACHITAT LA {{ now()->format('d.m.Y') }}</p>
            <p style="font-size:28px;font-weight:700;color:#0C2340;margin:0;">{{ number_format($sold, 2) }} Ron</p>
        </div>
        <hr style="border:none;border-top:1px solid #f3f4f6;margin:20px 0;">
        <p style="font-size:12px;color:#9CA3AF;text-align:center;line-height:1.6;">
            Acest mesaj a fost generat automat. Va rugam sa nu dati reply.<br>
            Va multumim pentru cooperare si intelegere!
        </p>
        <p style="font-size:13px;color:#16a34a;font-style:italic;text-align:center;margin-top:12px;">
            Va dorim o zi plina de energie si prosperitate!
        </p>
        <p style="font-size:13px;font-weight:600;color:#0C2340;text-align:center;">Echipa Aquaserv</p>
    </td>
</tr>
@endsection
