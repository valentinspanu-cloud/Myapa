<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('general.pages.login.title') - {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <script>window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?></script>
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
            background: #0C2340;
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
        .l-logos img.logo-aquaserv { height: 32px; object-fit: contain; opacity: 1; background: rgba(255,255,255,0.9); padding: 3px 6px; border-radius: 4px; }

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
            color: rgba(255,255,255,0.75);
            line-height: 1.75;
            margin-bottom: 2rem;
            max-width: 300px;
        }

        .l-features { display: flex; flex-direction: column; gap: 11px; }
        .l-feature { display: flex; align-items: center; gap: 10px; }
        .l-feature-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .l-feature-icon svg { width: 14px; height: 14px; stroke: rgba(255,255,255,0.7); }
        .l-feature span { font-size: 13px; color: rgba(255,255,255,0.80); }

        .l-left-footer {
            position: relative;
            z-index: 1;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .l-left-footer a { font-size: 11px; color: rgba(255,255,255,0.6); text-decoration: none; transition: color 0.15s; }
        .l-left-footer a:hover { color: rgba(255,255,255,0.65); }

        .l-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .l-topnav {
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            border-bottom: 1px solid #f5f5f5;
        }
        .l-topnav a { font-size: 12px; color: #6b7280; text-decoration: none; transition: color 0.15s; }
        .l-topnav a:hover { color: #1a5276; }

        .l-form-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .l-form-wrap { width: 100%; max-width: 360px; }

        .l-brand { margin-bottom: 2rem; }
        .l-brand-title { font-size: 22px; font-weight: 600; color: #1a5276; margin-bottom: 4px; }
        .l-brand-sub { font-size: 13px; color: #9ca3af; }

        .l-alert {
            display: flex; align-items: flex-start; gap: 8px;
            padding: 10px 13px; border-radius: 8px; font-size: 13px;
            margin-bottom: 1rem; line-height: 1.5;
        }
        .l-alert.err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .l-alert.ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .l-alert svg { flex-shrink: 0; margin-top: 1px; }
        .l-alert button {
            margin-left: auto; background: none; border: none; cursor: pointer;
            color: inherit; opacity: 0.5; font-size: 16px; line-height: 1; padding: 0; flex-shrink: 0;
        }

        .l-field { margin-bottom: 12px; }
        .l-field-label {
            display: block;
            font-size: 11px; font-weight: 600; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px;
        }
        .l-field-inner { position: relative; }
        .l-field-inner > svg:first-child {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 16px; height: 16px; stroke: #9ca3af; pointer-events: none; z-index: 2;
        }
        .l-field-inner input {
            display: block; width: 100%; height: 44px;
            padding: 0 44px 0 38px; position: relative; z-index: 0;
            font-size: 14px; color: #111827; background: #f9fafb;
            border: 1.5px solid #e5e7eb; border-radius: 8px; outline: none;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        }
        .l-field-inner input:focus {
            border-color: #1a5276; background: white;
            box-shadow: 0 0 0 3px rgba(26,82,118,0.1);
        }

        .l-options {
            display: flex; justify-content: space-between;
            align-items: center; margin: 14px 0 1.5rem;
        }
        .l-remember {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #6b7280; cursor: pointer; user-select: none;
        }
        .l-remember input { width: 15px; height: 15px; accent-color: #1a5276; cursor: pointer; }
        .l-forgot { font-size: 13px; color: #1a5276; text-decoration: none; }
        .l-forgot:hover { text-decoration: underline; }

        .l-recaptcha {
            background: #f9fafb; border: 1px solid #f0f0f0;
            border-radius: 8px; padding: 9px 12px;
            font-size: 11px; color: #9ca3af; line-height: 1.6; margin-bottom: 1.25rem;
        }
        .l-recaptcha a { color: #1a5276; text-decoration: none; }
        .l-recaptcha a:hover { text-decoration: underline; }

        .pwd-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 4px;
            color: #1a5276; display: flex; align-items: center; justify-content: center; z-index: 10;
        }
        .pwd-toggle svg { width: 18px; height: 18px; stroke: currentColor; pointer-events: none; }

        .l-btn-submit {
            display: block; width: 100%; height: 46px; background: #0C2340;
            color: white; font-size: 14px; font-weight: 600; border: none;
            border-radius: 8px; cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            letter-spacing: 0.02em; margin-bottom: 14px;
        }
        .l-btn-submit:hover { background: #154360; }
        .l-btn-submit:active { transform: scale(0.99); }

        .l-divider { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .l-divider::before, .l-divider::after { content: ''; flex: 1; height: 1px; background: #f0f0f0; }
        .l-divider span { font-size: 11px; color: #d1d5db; }

        .l-btn-register {
            display: flex; align-items: center; justify-content: center;
            width: 100%; height: 44px; background: white; color: #374151;
            font-size: 14px; border: 1.5px solid #e5e7eb; border-radius: 8px;
            cursor: pointer; text-decoration: none; transition: border-color 0.15s, color 0.15s;
        }
        .l-btn-register:hover { border-color: #1a5276; color: #1a5276; }

        .l-form-footer {
            padding: 1rem 2rem; text-align: center;
            font-size: 11px; color: #d1d5db; border-top: 1px solid #f5f5f5;
        }

        @media (max-width: 800px) {
            .l-left { display: none; }
            .l-topnav { padding: 1rem 1.5rem; }
            .l-form-area { padding: 1.5rem; }
            .l-mobile-cms { display: block !important; }
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
        <h2>Portalul clientilor<br><span style="color:#5DCAA5;">Aquaserv Tulcea</span></h2>
        <p>Acceseaza serviciile de apa online &mdash; facturi, indecsi, notificari si multe altele, direct din browser.</p>

        <div class="l-features">
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                </div>
                <span>Vizualizeaza si plateste facturile online</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                </div>
                <span>Trimite citiri de index din orice dispozitiv</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                </div>
                <span>Primeste notificari si anunturi importante</span>
            </div>
            <div class="l-feature">
                <div class="l-feature-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </div>
                <span>Descarca facturi in format PDF</span>
            </div>
        </div>
    </div>

    <div class="l-left-footer">
        @if(getPage(5))<a href="{{ route('cms.view', getPage(5)['slug']) }}">{{ getPage(5)['title'] }}</a>@endif
        @if(getPage(6))<a href="{{ route('cms.view', getPage(6)['slug']) }}">{{ getPage(6)['title'] }}</a>@endif
        @if(getPage(7))<a href="{{ route('cms.view', getPage(7)['slug']) }}">{{ getPage(7)['title'] }}</a>@endif
        <a href="http://192.168.1.100/pagina/informatii-clienti-1">Informații clienți</a>
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
                <div class="l-brand-title">Autentificare</div>
                <div class="l-brand-sub">Introdu datele contului tau MyAPA</div>
            </div>

            @if($errors->has('email') || $errors->has('password'))
                <div class="l-alert err">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    @lang('general.pages.login.error')
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
            @foreach(['error','siverrors','api_failed','siverror'] as $s)
                @if(session($s))
                    <div class="l-alert err">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        {{ session($s) }}
                        <button onclick="this.parentElement.remove()">&times;</button>
                    </div>
                @endif
            @endforeach
            @if(session('success'))
                <div class="l-alert ok">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                @if(Request::has('previous'))
                    <input type="hidden" name="previous" value="{{ Request::get('previous') }}">
                @else
                    <input type="hidden" name="previous" value="{{ URL::previous() }}">
                @endif

                <div class="l-field">
                    <label class="l-field-label" for="email">@lang('labels.email')</label>
                    <div class="l-field-inner">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="email@exemplu.ro"
                            autocomplete="email" autofocus/>
                    </div>
                </div>

                <div class="l-field">
                    <label class="l-field-label" for="password">@lang('general.pages.login.password_label')</label>
                    <div class="l-field-inner">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        <input type="password" id="password" name="password"
                            placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                            autocomplete="current-password"/>
                        <button type="button" class="pwd-toggle" id="pwdToggle" aria-label="Arata parola">
                            <svg id="iconShow" style="pointer-events:none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <svg id="iconHide" style="display:none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                </div>

                <div class="l-options">
                    <label class="l-remember">
                        <input type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                        @lang('general.pages.login.remember')
                    </label>
                    <a href="{{ route('password.request') }}" class="l-forgot">
                        @lang('general.pages.login.recover_btn')
                    </a>
                </div>

                <div id="login_id"></div>
                {!! GoogleReCaptchaV3::renderOne('login_id', 'login_id') !!}

                <div class="l-recaptcha">
                    Protejat de reCAPTCHA v3 &middot;
                    <a href="https://policies.google.com/privacy" target="_blank">Confidentialitate</a> &middot;
                    <a href="https://policies.google.com/terms" target="_blank">Termeni</a>
                </div>

                <button type="submit" class="l-btn-submit">
                    @lang('general.pages.login.login_btn')
                </button>
            </form>

            <div class="l-divider"><span>sau</span></div>

            <a href="{{ route('register') }}" class="l-btn-register">
                @lang('general.pages.login.new_account')
            </a>

            @if(getPage(4))
            <div style="display:none;text-align:center;margin-top:16px;" class="l-mobile-cms">
                <a href="{{ route('cms.view', getPage(4)['slug']) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#1a5276;text-decoration:none;font-weight:500;">
                    Informatii clienti
                </a>
            </div>
            @endif

        </div>
    </div>

    <div class="l-form-footer">
        COPYRIGHT &copy; {{ date('Y') }} {{ env('APP_NAME') }} &middot; Aquaserv Tulcea
    </div>

</div>

<script src="{{ asset('js/app.js') }}"></script>
{!! GoogleReCaptchaV3::init() !!}
<script>var ajaxUrl = '{{ env('APP_URL') }}';</script>
<script>
(function() {
    var btn = document.getElementById('pwdToggle');
    var input = document.getElementById('password');
    var show = document.getElementById('iconShow');
    var hide = document.getElementById('iconHide');
    if (btn && input) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (input.type === 'password') {
                input.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
            } else {
                input.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
            }
            input.focus();
        });
    }
})();
</script>
</body>
</html>