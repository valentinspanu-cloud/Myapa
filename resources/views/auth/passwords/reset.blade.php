@extends('layouts.login')
@section('title', trans('general.pages.reset.title'))
@section('content')
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, sans-serif; }
body { display: flex; min-height: 100vh; background: white; }
.l-left {
    width: 420px; flex-shrink: 0; background: #0C2340;
    min-height: 100vh; display: flex; flex-direction: column;
    padding: 2.5rem 2.5rem 2rem; position: relative; overflow: hidden;
}
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
.l-left-footer a:hover { color:rgba(255,255,255,0.9); }
.l-right { flex:1; display:flex; flex-direction:column; background:white; }
.l-form-area { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem; }
.l-form-wrap { width:100%; max-width:380px; }
.l-brand { margin-bottom:2rem; }
.l-brand-title { font-size:22px; font-weight:600; color:#1a5276; margin-bottom:4px; }
.l-brand-sub { font-size:13px; color:#9ca3af; }
.l-alert { display:flex; align-items:flex-start; gap:8px; padding:10px 13px; border-radius:8px; font-size:13px; margin-bottom:1rem; line-height:1.5; }
.l-alert.err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.pass-field-group { margin-bottom:16px; }
.pass-label { display:block; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.07em; margin-bottom:5px; }
.pass-field-group input[type=email],
.pass-field-group input[type=text] {
    display:block; width:100%; height:44px; padding:0 12px;
    font-size:14px; color:#111827; background:#f9fafb;
    border:1.5px solid #e5e7eb; border-radius:8px; outline:none;
    transition:border-color 0.15s, box-shadow 0.15s;
}
.pass-field-group input:focus { border-color:#1a5276; background:white; box-shadow:0 0 0 3px rgba(26,82,118,0.1); }
.password-input-wrap { position:relative; display:flex; align-items:center; }
.password-input-wrap input { width:100% !important; padding-right:40px !important; height:44px; font-size:14px; color:#111827; background:#f9fafb; border:1.5px solid #e5e7eb; border-radius:8px; outline:none; padding-left:12px; transition:border-color 0.15s; }
.password-input-wrap input:focus { border-color:#1a5276; background:white; box-shadow:0 0 0 3px rgba(26,82,118,0.1); }
.toggle-pass { position:absolute; right:10px; background:none; border:none; cursor:pointer; padding:4px; color:#9ca3af; display:flex; align-items:center; z-index:10; }
.toggle-pass:hover { color:#1a5276; }
.l-hints { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; font-size:12px; margin-bottom:12px; }
.l-hints-title { font-weight:600; color:#374151; margin-bottom:6px; }
.l-hints ul { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:3px; }
.l-match-msg { font-size:12px; margin-bottom:12px; }
.l-btn-submit { display:block; width:100%; height:46px; background:#1a5276; color:white; font-size:14px; font-weight:600; border:none; border-radius:8px; cursor:pointer; transition:background 0.15s; margin-bottom:14px; }
.l-btn-submit:hover { background:#154360; }
.l-back { display:flex; justify-content:flex-end; }
.l-back a { font-size:13px; color:#1a5276; text-decoration:none; }
.l-back a:hover { text-decoration:underline; }
.l-form-footer { padding:1rem 2rem; text-align:center; font-size:11px; color:#d1d5db; border-top:1px solid #f5f5f5; }
.invalid-feedback { font-size:12px; color:#dc2626; margin-top:4px; display:block; }
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
        <h2>Resetare<br><span style="color:#5DCAA5;">parolă</span></h2>
        <p>Introdu noua parolă pentru contul tău MyAPA. Parola trebuie să fie sigură și ușor de reținut.</p>
        <div class="l-features">
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                </div>
                <span>Parolă sigură cu caractere speciale</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                </div>
                <span>Contul tău este protejat</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </div>
                <span>Link trimis pe emailul tău</span>
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
            <div class="l-brand">
                <div class="l-brand-title">Resetare parolă</div>
                <div class="l-brand-sub">Introdu noua parolă pentru contul tău</div>
            </div>

            @if ($errors->has('password_confirmation') || $errors->has('password'))
                <div class="l-alert err">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    {{ $errors->first('password') ?: $errors->first('password_confirmation') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/password/reset') }}">
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="pass-field-group">
                    <label class="pass-label">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ $email ?? old('email') }}"
                           autocomplete="off" readonly onfocus="this.removeAttribute('readonly')"/>
                </div>

                <div class="pass-field-group">
                    <label class="pass-label">@lang('general.pages.login.password_label')</label>
                    <div class="password-input-wrap">
                        <input type="password" id="password" name="password" autocomplete="new-password"/>
                        <button type="button" class="toggle-pass" data-target="password">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="l-hints" id="pwdHints" style="display:none;">
                    <div class="l-hints-title">Parola trebuie să conțină:</div>
                    <ul>
                        <li id="hint-special" style="color:#9ca3af;">✗ un caracter special</li>
                        <li id="hint-upper" style="color:#9ca3af;">✗ un caracter mare</li>
                        <li id="hint-number" style="color:#9ca3af;">✗ o cifră</li>
                        <li id="hint-length" style="color:#9ca3af;">✗ minim 6 caractere</li>
                    </ul>
                </div>

                <div class="pass-field-group">
                    <label class="pass-label">@lang('general.pages.register.confirm_label')</label>
                    <div class="password-input-wrap">
                        <input type="password" id="password-confirm" name="password_confirmation" autocomplete="new-password"/>
                        <button type="button" class="toggle-pass" data-target="password-confirm">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="l-match-msg" id="matchMsg"></div>

                <button type="submit" class="l-btn-submit">
                    @lang('general.pages.recover.reset_btn')
                </button>

                <div class="l-back">
                    <a href="{{ route('login') }}">@lang('general.pages.recover.back_btn')</a>
                </div>
            </form>
        </div>
    </div>
    <div class="l-form-footer">
        COPYRIGHT &copy; {{ date('Y') }} {{ env('APP_NAME') }} &middot; Aquaserv Tulcea
    </div>
</div>

<script>
(function() {
    var pwdInput = document.getElementById('password');
    var confirmInput = document.getElementById('password-confirm');
    var hints = document.getElementById('pwdHints');
    var matchMsg = document.getElementById('matchMsg');
    var hSpecial = document.getElementById('hint-special');
    var hUpper = document.getElementById('hint-upper');
    var hNumber = document.getElementById('hint-number');
    var hLength = document.getElementById('hint-length');

    function setHint(el, valid, text) {
        el.style.color = valid ? '#16a34a' : '#9ca3af';
        el.textContent = (valid ? '✓ ' : '✗ ') + text;
    }

    function checkMatch() {
        if (!confirmInput.value) { matchMsg.textContent=''; return; }
        var match = pwdInput.value === confirmInput.value;
        matchMsg.style.color = match ? '#16a34a' : '#dc2626';
        matchMsg.textContent = match ? '✓ Parolele coincid' : '✗ Parolele nu sunt identice';
    }

    if (pwdInput) {
        pwdInput.addEventListener('focus', function() { hints.style.display='block'; });
        pwdInput.addEventListener('blur', function() { if (!pwdInput.value) hints.style.display='none'; });
        pwdInput.addEventListener('input', function() {
            var v = pwdInput.value;
            setHint(hSpecial, /[^a-zA-Z0-9]/.test(v), 'un caracter special');
            setHint(hUpper, /[A-Z]/.test(v), 'un caracter mare');
            setHint(hNumber, /[0-9]/.test(v), 'o cifră');
            setHint(hLength, v.length >= 6, 'minim 6 caractere');
            checkMatch();
        });
    }
    if (confirmInput) { confirmInput.addEventListener('input', checkMatch); }
})();
</script>
@endsection
