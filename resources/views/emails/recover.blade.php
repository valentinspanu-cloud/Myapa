@extends('layouts.email')
@section('title', trans('general.pages.recover.title'))
@section('preHeader', 'Resetare parolă cont MyAPA')
@section('content')
<tr>
    <td style="padding: 0 0 20px 0;">
        <div style="width: 48px; height: 48px; background: #FAEEDA; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
            <span style="font-size: 24px;">🔐</span>
        </div>
        <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin-bottom: 8px;">Resetare parolă</h2>
        <p style="font-size: 14px; color: #6B7280; line-height: 1.6; margin-bottom: 8px;">@lang('general.emails.general.dear_customer'),</p>
        <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 24px;">
            {!! trans('general.emails.recover.p1') !!}
        </p>
        <a href="{{ url('password/reset', $token) }}" target="_blank"
           style="display: inline-block; background: #0C2340; color: #ffffff; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 8px; text-decoration: none; letter-spacing: 0.02em;">
            🔑 Resetează parola
        </a>
        <p style="font-size: 12px; color: #9CA3AF; margin-top: 24px; line-height: 1.6;">
            Dacă nu ați solicitat resetarea parolei, puteți ignora acest email.<br>
            Link-ul expiră în 60 de minute.
        </p>
        <hr style="border: none; border-top: 1px solid #F3F4F6; margin: 24px 0;">
        <p style="font-size: 12px; color: #9CA3AF;">
            Sau copiați link-ul în browser:<br>
            <span style="color: #0C2340; word-break: break-all;">{{ url('password/reset', $token) }}</span>
        </p>
    </td>
</tr>
@endsection
