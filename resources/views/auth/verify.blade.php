@extends('layouts.login')
@section('title', trans('general.pages.register.title'))
@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, sans-serif; }
body { display: flex; min-height: 100vh; background: white; }
.l-left { width: 420px; flex-shrink: 0; background: #0C2340; min-height: 100vh; display: flex; flex-direction: column; padding: 2.5rem 2.5rem 2rem; position: relative; overflow: hidden; }
.l-left::before { content:''; position:absolute; top:-80px; right:-80px; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
.l-left::after { content:''; position:absolute; bottom:-60px; left:-60px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
.l-logos { display:flex; align-items:center; gap:14px; position:relative; z-index:1; }
.l-logo-divider { width:1px; height:28px; background:rgba(255,255,255,0.2); }
.l-logos img.logo-myapa { height:32px; object-fit:contain; }
.l-logos img.logo-aquaserv { height:32px; object-fit:contain; background:rgba(255,255,255,0.9); padding:3px 6px; border-radius:4px; }
.l-middle { flex:1; display:flex; flex-direction:column; justify-content:center; position:relative; z-index:1; padding:2.5rem 0 2rem; }
.l-middle h2 { font-size:24px; font-weight:600; color:white; line-height:1.4; margin-bottom:12px; }
.l-middle p { font-size:13px; color:rgba(255,255,255,0.75); line-height:1.75; margin-bottom:2rem; max-width:300px; }
.l-features { display:flex; flex-direction:column; gap:11px; }
.l-feature { display:flex; align-items:center; gap:10px; }
.l-feature-icon { width:28px; height:28px; border-radius:7px; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.l-feature-icon svg { width:14px; height:14px; stroke:rgba(255,255,255,0.7); }
.l-feature span { font-size:13px; color:rgba(255,255,255,0.80); }
.l-left-footer { position:relative; z-index:1; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.08); display:flex; gap:14px; flex-wrap:wrap; }
.l-left-footer a { font-size:11px; color:rgba(255,255,255,0.6); text-decoration:none; }
.l-right { flex:1; display:flex; flex-direction:column; background:white; }
.l-form-area { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem; }
.l-form-wrap { width:100%; max-width:400px; text-align:center; }
.verify-icon { width:72px; height:72px; background:#E1F5EE; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; font-size:32px; }
.l-brand-title { font-size:22px; font-weight:600; color:#1a5276; margin-bottom:8px; }
.l-brand-sub { font-size:14px; color:#6B7280; line-height:1.6; margin-bottom:24px; }
.l-alert-ok { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:8px; padding:10px 14px; font-size:13px; margin-bottom:16px; }
.verify-resend-btn { background:none; border:none; cursor:pointer; color:#1a5276; font-size:14px; font-weight:500; text-decoration:underline; padding:0; }
.verify-resend-btn:hover { color:#154360; }
.l-btn-back { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:#9ca3af; text-decoration:none; margin-top:24px; }
.l-btn-back:hover { color:#1a5276; }
.l-form-footer { padding:1rem 2rem; text-align:center; font-size:11px; color:#d1d5db; border-top:1px solid #f5f5f5; }
.l-mobile-logo { display:none; text-align:center; margin-bottom:24px; }
.l-mobile-logo img { height:60px; object-fit:contain; }
@media (max-width:800px) { .l-left { display:none; } .l-form-area { padding:1.5rem; } .l-mobile-logo { display:block !important; } }
</style>

<div class="l-left">
    <div class="l-logos">
        <img src="{{ asset('img/logo.png') }}" alt="MyAPA" class="logo-myapa"/>
        <div class="l-logo-divider"></div>
        <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea" class="logo-aquaserv"/>
    </div>
    <div class="l-middle">
        <h2>Un pas mai<br><span style="color:#5DCAA5;">aproape!</span></h2>
        <p>Verificați emailul pentru a activa contul și a accesa toate serviciile online Aquaserv Tulcea.</p>
        <div class="l-features">
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </div>
                <span>Email trimis la adresa înregistrată</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <span>Dați click pe butonul din email</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z"/></svg>
                </div>
                <span>Contul se activează automat</span>
            </div>
        </div>
    </div>
    <div class="l-left-footer">
        @if(getPage(5))<a href="{{ route('cms.view', getPage(5)['slug']) }}">{{ getPage(5)['title'] }}</a>@endif
        @if(getPage(6))<a href="{{ route('cms.view', getPage(6)['slug']) }}">{{ getPage(6)['title'] }}</a>@endif
        <span style="font-size:11px;color:rgba(255,255,255,0.2);margin-left:auto">&copy; {{ date('Y') }} Aquaserv Tulcea</span>
    </div>
</div>

<div class="l-right">
    <div class="l-form-area">
        <div class="l-form-wrap">
            <div class="l-mobile-logo">
                <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea"/>
            </div>
            <div class="verify-icon">✉️</div>
            <div class="l-brand-title">Verificați adresa de email</div>
            <div class="l-brand-sub">
                @lang('general.pages.login.verify_paragraph1')
            </div>

            @if (session('resent'))
                <div class="l-alert-ok">
                    ✓ @lang('general.pages.login.verify_resent')
                </div>
            @endif

            <p style="font-size:13px;color:#6B7280;margin-bottom:16px;">
                @lang('general.pages.login.verify_paragraph2'),
            </p>

            <form method="POST" action="{{ route('verification.resend') }}" style="display:inline">
                @csrf
                <button type="submit" class="verify-resend-btn">
                    @lang('general.pages.login.verify_btn')
                </button>
            </form>

            <br>
            <a href="{{ route('login') }}" class="l-btn-back">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Înapoi la autentificare
            </a>
        </div>
    </div>
    <div class="l-form-footer">
        COPYRIGHT &copy; {{ date('Y') }} {{ env('APP_NAME') }} &middot; Aquaserv Tulcea
    </div>
</div>
@endsection
