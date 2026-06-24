@extends('layouts.email')
@section('title', trans('general.emails.verify.title'))
@section('preHeader', 'Confirmați adresa de email pentru contul MyAPA')
@section('content')
<tr>
    <td style="padding: 0 0 20px 0;">
        <div style="width: 48px; height: 48px; background: #E1F5EE; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
            <span style="font-size: 24px;">✉️</span>
        </div>
        <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 8px;">Confirmați adresa de email</h2>
        <p style="font-size: 14px; color: #6B7280; line-height: 1.6; margin-bottom: 8px;">@lang('general.emails.general.dear_customer'),</p>
        <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 24px;">
            Vă mulțumim pentru înregistrarea în portalul clienților <strong>Aquaserv Tulcea</strong>. 
            Confirmați că aceasta este adresa dumneavoastră de email pentru a activa contul.
        </p>
        <a href="{{ $url }}" onclick="setTimeout(function(){window.close();},500);"
           style="display: inline-block; background: #0C2340; color: #ffffff; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 8px; text-decoration: none; letter-spacing: 0.02em;">
            ✓ Confirmă adresa de email
        </a>
        <p style="font-size: 12px; color: #9CA3AF; margin-top: 24px; line-height: 1.6;">
            Dacă nu ați creat un cont, puteți ignora acest email.<br>
            Link-ul expiră în 60 de minute.
        </p>
        <hr style="border: none; border-top: 1px solid #F3F4F6; margin: 24px 0;">
        <p style="font-size: 12px; color: #9CA3AF;">
            Sau copiați link-ul în browser:<br>
            <span style="color: #0C2340; word-break: break-all;">{{ $url }}</span>
        </p>
    </td>
</tr>
@endsection
