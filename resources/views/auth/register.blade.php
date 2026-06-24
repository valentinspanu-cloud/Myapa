@extends('layouts.login')
@section('title', trans('general.pages.register.title'))
@section('content')
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('general.pages.register.title') - {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: white;
        }

        .l-left {
            width: 420px;
            flex-shrink: 0;
            background: #1a5276;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .l-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }
        .l-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }
        .l-left-accent {
            position: absolute;
            top: 40%; right: -40px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            pointer-events: none;
        }
        .l-logos {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }
        .l-logo-divider {
            width: 1px;
            height: 28px;
            background: rgba(255,255,255,0.2);
        }
        .l-logos img.logo-myapa { height: 32px; object-fit: contain; }
        .l-logos img.logo-aquaserv { height: 28px; object-fit: contain; opacity: 0.75; }

        .l-tag {
            display: inline-block;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.85);
            font-size: 11px;
            font-weight: 500;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.04em;
            margin-bottom: 14px;
        }
        .l-middle {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
            padding: 2.5rem 0 2rem;
        }
        .l-middle h2 {
            font-size: 24px;
            font-weight: 600;
            color: white;
            line-height: 1.4;
            margin-bottom: 12px;
        }
        .l-middle p {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            line-height: 1.75;
            margin-bottom: 2rem;
            max-width: 300px;
        }
        .l-steps { display: flex; flex-direction: column; gap: 16px; }
        .l-step { display: flex; align-items: flex-start; gap: 12px; }
        .l-step-num {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.8);
            font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        .l-step-text { display: flex; flex-direction: column; gap: 2px; }
        .l-step-text strong { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85); }
        .l-step-text span { font-size: 12px; color: rgba(255,255,255,0.4); }

        .l-left-footer {
            position: relative; z-index: 1;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex; gap: 14px; flex-wrap: wrap;
        }
        .l-left-footer a { font-size: 11px; color: rgba(255,255,255,0.3); text-decoration: none; transition: color 0.15s; }
        .l-left-footer a:hover { color: rgba(255,255,255,0.65); }

        .l-right { flex: 1; display: flex; flex-direction: column; background: white; overflow-y: auto; }
        .l-form-area { flex: 1; display: flex; align-items: flex-start; justify-content: center; padding: 2.5rem 2rem; }
        .l-form-wrap { width: 100%; max-width: 420px; }

        .l-brand { margin-bottom: 1.75rem; }
        .l-brand-title { font-size: 22px; font-weight: 600; color: #1a5276; margin-bottom: 4px; }
        .l-brand-sub { font-size: 13px; color: #9ca3af; }

        .l-alert {
            display: flex; align-items: flex-start; gap: 8px;
            padding: 10px 13px; border-radius: 8px; font-size: 13px;
            margin-bottom: 1rem; line-height: 1.5;
        }
        .l-alert.err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .l-alert svg { flex-shrink: 0; margin-top: 1px; }
        .l-alert button {
            margin-left: auto; background: none; border: none; cursor: pointer;
            color: inherit; opacity: 0.5; font-size: 16px; line-height: 1; padding: 0; flex-shrink: 0;
        }

        .l-fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 12px; }
        .l-field { margin-bottom: 12px; }
        .l-field.full { grid-column: 1 / -1; }

        .l-field-label {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 600; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px;
        }
        .l-field-inner { position: relative; }
        .l-field-inner > svg:first-child {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 15px; height: 15px; stroke: #9ca3af; pointer-events: none; z-index: 2;
        }
        .l-field-inner input {
            display: block; width: 100%; height: 42px;
            padding: 0 12px 0 36px; position: relative; z-index: 0;
            font-size: 13.5px; color: #111827; background: #f9fafb;
            border: 1.5px solid #e5e7eb; border-radius: 8px; outline: none;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        }
        .l-field-inner input:focus {
            border-color: #1a5276; background: white;
            box-shadow: 0 0 0 3px rgba(26,82,118,0.1);
        }
        .l-field-inner input.is-invalid { border-color: #ef4444; background: #fff; }
        .l-field-inner input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .l-field-error { font-size: 11.5px; color: #dc2626; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

        .l-info-btn {
            display: inline-flex; align-items: center; justify-content: center;
            background: none; border: none; padding: 0; cursor: pointer;
            color: #f59e0b; position: relative;
        }
        .l-info-btn svg { width: 13px; height: 13px; }
        .l-info-btn .l-tooltip {
            display: none; position: absolute; left: 50%; bottom: calc(100% + 8px);
            transform: translateX(-50%); background: #1f2937; color: white;
            font-size: 11px; padding: 6px 10px; border-radius: 6px;
            white-space: nowrap; z-index: 100; pointer-events: none;
        }
        .l-info-btn:hover .l-tooltip { display: block; }

        .pwd-toggle {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 4px;
            color: #9ca3af; display: flex; align-items: center; justify-content: center;
            z-index: 10; transition: color 0.15s;
        }
        .pwd-toggle:hover { color: #1a5276; }
        .pwd-toggle svg { width: 16px; height: 16px; stroke: currentColor; pointer-events: none; }
        .has-toggle input { padding-right: 40px; }

        .l-pwd-hints {
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; display: none;
        }
        .l-pwd-hints.visible { display: block; }
        .l-pwd-hints-title {
            font-size: 11px; font-weight: 600; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 7px;
        }
        .l-pwd-hints ul { list-style: none; display: grid; grid-template-columns: 1fr 1fr; gap: 4px 8px; }
        .l-pwd-hints li {
            font-size: 12px; color: #9ca3af;
            display: flex; align-items: center; gap: 5px; transition: color 0.2s;
        }
        .l-pwd-hints li::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: #d1d5db; flex-shrink: 0; transition: background 0.2s;
        }
        .l-pwd-hints li.valid { color: #059669; }
        .l-pwd-hints li.valid::before { background: #059669; }

        .l-match-msg { font-size: 11.5px; margin-top: 4px; display: none; align-items: center; gap: 4px; }
        .l-match-msg.show { display: flex; }
        .l-match-msg.ok { color: #059669; }
        .l-match-msg.err { color: #dc2626; }

        .l-checkbox { display: flex; align-items: flex-start; gap: 9px; margin-bottom: 12px; }
        .l-checkbox input[type="checkbox"] { width: 16px; height: 16px; margin-top: 2px; accent-color: #1a5276; cursor: pointer; flex-shrink: 0; }
        .l-checkbox label { font-size: 12.5px; color: #6b7280; line-height: 1.55; cursor: pointer; }
        .l-checkbox label a { color: #1a5276; }
        .l-checkbox label a:hover { text-decoration: underline; }

        .l-recaptcha {
            background: #f9fafb; border: 1px solid #f0f0f0;
            border-radius: 8px; padding: 8px 12px; font-size: 11px;
            color: #9ca3af; line-height: 1.6; margin-bottom: 1rem;
        }
        .l-recaptcha a { color: #1a5276; text-decoration: none; }
        .l-recaptcha a:hover { text-decoration: underline; }

        .l-btn-submit {
            display: block; width: 100%; height: 46px; background: #1a5276;
            color: white; font-size: 14px; font-weight: 600; border: none;
            border-radius: 8px; cursor: pointer; transition: background 0.15s, transform 0.1s;
            letter-spacing: 0.02em; margin-bottom: 12px;
        }
        .l-btn-submit:hover { background: #154360; }
        .l-btn-submit:active { transform: scale(0.99); }

        .l-divider { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .l-divider::before, .l-divider::after { content: ''; flex: 1; height: 1px; background: #f0f0f0; }
        .l-divider span { font-size: 11px; color: #d1d5db; }

        .l-btn-login {
            display: flex; align-items: center; justify-content: center;
            width: 100%; height: 42px; background: white; color: #374151;
            font-size: 13.5px; border: 1.5px solid #e5e7eb; border-radius: 8px;
            cursor: pointer; text-decoration: none; transition: border-color 0.15s, color 0.15s;
        }
        .l-btn-login:hover { border-color: #1a5276; color: #1a5276; }

        .l-form-footer {
            padding: 1rem 2rem; text-align: center;
            font-size: 11px; color: #d1d5db; border-top: 1px solid #f5f5f5;
        }

        .l-section-sep { display: flex; align-items: center; gap: 8px; margin: 4px 0 12px; }
        .l-section-sep span {
            font-size: 10px; font-weight: 700; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 0.08em; white-space: nowrap;
        }
        .l-section-sep::before, .l-section-sep::after { content: ''; flex: 1; height: 1px; background: #f0f0f0; }

        @media (max-width: 800px) {
            .l-left { display: none; }
            .l-form-area { padding: 1.5rem; }
            .l-fields-grid { grid-template-columns: 1fr; }
            .l-pwd-hints ul { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- STANGA --}}
<div class="l-left">
    <div class="l-left-accent"></div>
    <div class="l-logos">
        <img src="{{ asset('img/logo.png') }}" alt="MyAPA" class="logo-myapa"/>
        <div class="l-logo-divider"></div>
        <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea" class="logo-aquaserv"/>
    </div>
    <div class="l-middle">
        <span class="l-tag">Cont nou</span>
        <h2>Creeaza-ti contul MyAPA in cativa pasi simpli</h2>
        <p>Ai nevoie de codul de client si numarul contractului, pe care le gasesti pe factura ta Aquaserv Tulcea.</p>
        <div class="l-steps">
            <div class="l-step">
                <div class="l-step-num">1</div>
                <div class="l-step-text">
                    <strong>Gaseste datele pe factura</strong>
                    <span>Cod client si numar contract</span>
                </div>
            </div>
            <div class="l-step">
                <div class="l-step-num">2</div>
                <div class="l-step-text">
                    <strong>Completeaza formularul</strong>
                    <span>Email, telefon si o parola sigura</span>
                </div>
            </div>
            <div class="l-step">
                <div class="l-step-num">3</div>
                <div class="l-step-text">
                    <strong>Activeaza contul</strong>
                    <span>Verificare prin email si esti gata</span>
                </div>
            </div>
            <div class="l-step">
                <div class="l-step-num">4</div>
                <div class="l-step-text">
                    <strong>Acceseaza serviciile online</strong>
                    <span>Facturi, indecsi, notificari si altele</span>
                </div>
            </div>
        </div>
    </div>
    <div class="l-left-footer">
        @if(getPage(5))<a href="{{ route('cms.view', getPage(5)['slug']) }}">{{ getPage(5)['title'] }}</a>@endif
        @if(getPage(6))<a href="{{ route('cms.view', getPage(6)['slug']) }}">{{ getPage(6)['title'] }}</a>@endif
        @if(getPage(7))<a href="{{ route('cms.view', getPage(7)['slug']) }}">{{ getPage(7)['title'] }}</a>@endif
        <span style="font-size:11px;color:rgba(255,255,255,0.2);margin-left:auto">&copy; {{ date('Y') }} Aquaserv Tulcea</span>
    </div>
</div>

{{-- DREAPTA --}}
<div class="l-right">
    <div class="l-form-area">
        <div class="l-form-wrap">
            <div class="l-mobile-logo">
                <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea"/>
            </div>

            <div class="l-brand">
                <div class="l-brand-title">Creare cont</div>
                <div class="l-brand-sub">Completeaza datele de pe factura ta Aquaserv</div>
            </div>

            @if(session('siverror'))
                <div class="l-alert err">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    {{ session('siverror') }}
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="l-section-sep"><span>Date din factura</span></div>
                <div class="l-fields-grid">

                    <div class="l-field">
                        <label class="l-field-label" for="cod-client">
                            @lang('labels.client_code')
                            <button type="button" class="l-info-btn" aria-label="Unde gasesc codul de client?">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 16v-4m0-4h.01"/></svg>
                                <span class="l-tooltip">Se gaseste pe factura, langa campul &bdquo;Cod abonat&rdquo;</span>
                            </button>
                        </label>
                        <div class="l-field-inner">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            <input type="text" id="cod-client" value="{{ old('client_code') }}" name="client_code"
                                   placeholder="ex: 12345"
                                   class="{{ $errors->has('client_code') ? 'is-invalid' : '' }}"/>
                        </div>
                        @if ($errors->has('client_code'))
                            <div class="l-field-error">{{ $errors->first('client_code') }}</div>
                        @endif
                    </div>

                    <div class="l-field">
                        <label class="l-field-label" for="nr-contract">
                            @lang('labels.contract_nr')
                            <button type="button" class="l-info-btn" aria-label="Unde gasesc numarul de contract?">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 16v-4m0-4h.01"/></svg>
                                <span class="l-tooltip">Se gaseste pe factura, langa campul &bdquo;Nr. Contract&rdquo;</span>
                            </button>
                        </label>
                        <div class="l-field-inner">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <input type="text" id="nr-contract" value="{{ old('contract_nr') }}" name="contract_nr"
                                   placeholder="ex: TL/xxxxx"
                                   class="{{ $errors->has('contract_nr') ? 'is-invalid' : '' }}"/>
                        </div>
                        @if ($errors->has('contract_nr'))
                            <div class="l-field-error">{{ $errors->first('contract_nr') }}</div>
                        @endif
                    </div>

                </div>

                <div class="l-section-sep"><span>Date de contact</span></div>

                <div class="l-field">
                    <label class="l-field-label" for="email">@lang('labels.email')</label>
                    <div class="l-field-inner">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        <input type="email" id="email" value="{{ old('email') }}" name="email"
                               placeholder="email@exemplu.ro" autocomplete="email"
                               class="{{ $errors->has('email') ? 'is-invalid' : '' }}"/>
                    </div>
                    @if ($errors->has('email'))
                        <div class="l-field-error">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="l-field">
                    <label class="l-field-label" for="phone">@lang('labels.phone')</label>
                    <div class="l-field-inner">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        <input type="text" id="phone" value="{{ old('phone') }}" name="phone"
                               placeholder="07xx xxx xxx"
                               autocomplete="off"
                               class="{{ $errors->has('phone') ? 'is-invalid' : '' }}"/>
                    </div>
                    @if ($errors->has('phone'))
                        <div class="l-field-error">{{ $errors->first('phone') }}</div>
                    @endif
                </div>

                <div class="l-section-sep"><span>Securitate</span></div>

                <div class="l-field">
                    <label class="l-field-label" for="password">@lang('general.pages.register.password_label')</label>
                    <div class="l-field-inner has-toggle">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        <input type="password" id="password" autocomplete="new-password" name="password"
                               placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                               class="{{ $errors->has('password') ? 'is-invalid' : '' }}"/>
                        <button type="button" class="pwd-toggle" id="pwdToggle1" aria-label="Arata parola">
                            <svg id="pwdShow1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg id="pwdHide1" style="display:none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <div class="l-field-error">{!! $errors->first('password') !!}</div>
                    @endif
                </div>

                <div class="l-pwd-hints" id="pwdHints">
                    <div class="l-pwd-hints-title">Parola trebuie sa contina</div>
                    <ul>
                        <li id="hint-special">un caracter special</li>
                        <li id="hint-upper">un caracter mare</li>
                        <li id="hint-number">o cifra</li>
                        <li id="hint-length">minim 6 caractere</li>
                    </ul>
                </div>

                <div class="l-field">
                    <label class="l-field-label" for="repeat-password">@lang('general.pages.register.confirm_label')</label>
                    <div class="l-field-inner has-toggle">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        <input type="password" id="repeat-password" autocomplete="new-password" name="password_confirmation"
                               placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                               class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"/>
                        <button type="button" class="pwd-toggle" id="pwdToggle2" aria-label="Arata confirmarea parolei">
                            <svg id="pwdShow2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg id="pwdHide2" style="display:none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <div class="l-match-msg" id="matchMsg"></div>
                    @if ($errors->has('password_confirmation'))
                        <div class="l-field-error">{{ $errors->first('password_confirmation') }}</div>
                    @endif
                </div>

                <div class="l-checkbox">
                    <input type="checkbox" id="agree" name="agree" value="1"/>
                    <label for="agree">{!! trans('labels.agree', ['gdpr' => route('cms.view', getPage(5)['slug'])]) !!}</label>
                </div>
                @if ($errors->has('agree'))
                    <div class="l-field-error" style="margin-bottom:10px">{{ $errors->first('agree') }}</div>
                @endif

                <div id="login_id"></div>
                {!! GoogleReCaptchaV3::renderOne('login_id', 'login_id') !!}

                <div class="l-recaptcha">
                    Protejat de reCAPTCHA v3 &middot;
                    <a href="https://policies.google.com/privacy" target="_blank">Confidentialitate</a> &middot;
                    <a href="https://policies.google.com/terms" target="_blank">Termeni</a>
                </div>

                <button type="submit" class="l-btn-submit">
                    @lang('general.pages.register.register_btn')
                </button>
            </form>

            <div class="l-divider"><span>sau</span></div>

            <a href="{{ route('login') }}" class="l-btn-login">
                @lang('general.pages.register.login_btn')
            </a>

        </div>
    </div>

    <div class="l-form-footer">
        COPYRIGHT &copy; {{ date('Y') }} {{ env('APP_NAME') }} &middot; Aquaserv Tulcea
    </div>
</div>

<script>
(function () {
    function makeToggle(btnId, showId, hideId, inputId) {
        var btn = document.getElementById(btnId);
        var show = document.getElementById(showId);
        var hide = document.getElementById(hideId);
        var inp = document.getElementById(inputId);
        if (!btn || !inp) return;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (inp.type === 'password') {
                inp.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
            } else {
                inp.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
            }
            inp.focus();
        });
    }
    makeToggle('pwdToggle1', 'pwdShow1', 'pwdHide1', 'password');
    makeToggle('pwdToggle2', 'pwdShow2', 'pwdHide2', 'repeat-password');

    var pwdInput = document.getElementById('password');
    var hints = document.getElementById('pwdHints');
    var hSpecial = document.getElementById('hint-special');
    var hUpper = document.getElementById('hint-upper');
    var hNumber = document.getElementById('hint-number');
    var hLength = document.getElementById('hint-length');

    function checkHint(el, valid) { el.classList.toggle('valid', valid); }

    if (pwdInput) {
        pwdInput.addEventListener('focus', function () { hints.classList.add('visible'); });
        pwdInput.addEventListener('blur', function () { if (!pwdInput.value) hints.classList.remove('visible'); });
        pwdInput.addEventListener('input', function () {
            var v = pwdInput.value;
            checkHint(hSpecial, /[^a-zA-Z0-9]/.test(v));
            checkHint(hUpper, /[A-Z]/.test(v));
            checkHint(hNumber, /[0-9]/.test(v));
            checkHint(hLength, v.length >= 6);
            checkMatch();
        });
    }

    var confirmInput = document.getElementById('repeat-password');
    var matchMsg = document.getElementById('matchMsg');

    function checkMatch() {
        if (!confirmInput || !confirmInput.value) { matchMsg.classList.remove('show'); return; }
        var match = pwdInput.value === confirmInput.value;
        matchMsg.classList.add('show');
        matchMsg.classList.toggle('ok', match);
        matchMsg.classList.toggle('err', !match);
        matchMsg.textContent = match ? '\u2713 Parolele coincid' : '\u2717 Parolele nu sunt identice';
    }

    if (confirmInput) { confirmInput.addEventListener('input', checkMatch); }
})();
</script>
</body>
</html>
@endsection
