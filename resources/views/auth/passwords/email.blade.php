@extends('layouts.login')
@section('title', trans('general.pages.recover.title'))
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
.l-left-footer { position:relative; z-index:1; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.08); display:flex; gap:14px; flex-wrap:wrap; }
.l-left-footer a { font-size:11px; color:rgba(255,255,255,0.6); text-decoration:none; }
.l-right { flex:1; display:flex; flex-direction:column; background:white; }
.l-form-area { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem; }
.l-form-wrap { width:100%; max-width:400px; }
.l-title { font-size:22px; font-weight:600; color:#1a5276; margin-bottom:6px; }
.l-sub { font-size:14px; color:#6B7280; margin-bottom:24px; line-height:1.5; }
.l-field { margin-bottom:16px; }
.l-field label { display:block; font-size:11px; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; }
.l-field input { width:100%; padding:11px 14px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:14px; color:#111827; outline:none; transition:border-color .15s; background:white; }
.l-field input:focus { border-color:#1a5276; }
.l-btn { width:100%; padding:12px; background:#0C2340; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; margin-top:8px; }
.l-btn:hover { background:#154360; }
.l-alert-ok { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:8px; padding:10px 14px; font-size:13px; margin-bottom:16px; }
.l-back { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:#9ca3af; text-decoration:none; margin-top:16px; }
.l-back:hover { color:#1a5276; }
.l-form-footer { padding:1rem 2rem; text-align:center; font-size:11px; color:#d1d5db; border-top:1px solid #f5f5f5; }
.l-mobile-logo { display:none; text-align:center; margin-bottom:24px; }
.l-mobile-logo img { height:60px; object-fit:contain; }
@media (max-width:800px) { .l-left { display:none; } .l-form-area { padding:1.5rem; } .l-mobile-logo { display:block; } }
</style>

<div class="l-left">
    <div class="l-logos">
        <img src="{{ asset('img/logo.png') }}" alt="MyAPA" class="logo-myapa"/>
        <div class="l-logo-divider"></div>
        <img src="{{ asset('img/aqua.png') }}" alt="Aquaserv Tulcea" class="logo-aquaserv"/>
    </div>
    <div class="l-middle">
        <h2>Recuperare<br><span style="color:#5DCAA5;">parolă</span></h2>
        <p>Introduceți adresa de email și vă vom trimite instrucțiuni pentru resetarea parolei.</p>
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
            <div class="l-title">Recuperare parolă</div>
            <div class="l-sub">Introduceți emailul contului pentru a primi linkul de resetare.</div>

            @if (session('status'))
                <div class="l-alert-ok">✓ {{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="l-field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@exemplu.ro" required autofocus/>
                    @if ($errors->has('email'))
                        <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $errors->first('email') }}</div>
                    @endif
                </div>
                <button type="submit" class="l-btn">Trimite link de resetare</button>
            </form>

            <a href="{{ route('login') }}" class="l-back">
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
