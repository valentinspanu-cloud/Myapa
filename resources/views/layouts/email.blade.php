<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ env('APP_NAME') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #F4F5F7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-text-size-adjust: none; }
        .email-wrapper { width: 100%; background: #F4F5F7; padding: 40px 20px; }
        .email-container { max-width: 560px; margin: 0 auto; }
        .email-header { background: #0C2340; border-radius: 12px 12px 0 0; padding: 24px 32px; display: flex; align-items: center; gap: 14px; }
        .email-header-divider { width: 1px; height: 28px; background: rgba(255,255,255,0.2); }
        .email-header img { height: 30px; object-fit: contain; }
        .email-header img.logo-aquaserv { background: rgba(255,255,255,0.9); padding: 3px 6px; border-radius: 4px; }
        .email-body { background: #ffffff; padding: 32px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; }
        .email-footer { background: #0C2340; border-radius: 0 0 12px 12px; padding: 16px 32px; text-align: center; }
        .email-footer p { font-size: 11px; color: rgba(255,255,255,0.35); }
        .email-footer a { color: rgba(255,255,255,0.5); text-decoration: none; }
        .preheader { display: none; max-height: 0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
<span class="preheader">@yield('preHeader')</span>
<div class="email-wrapper">
    <div class="email-container">
        <div class="email-header" style="justify-content:center;gap:20px;">
            <img src="https://my.aquaservtulcea.ro/img/logo.png" alt="MyAPA" class="logo-myapa" style="height:40px;"/>
            <div class="email-header-divider"></div>
            <img src="https://my.aquaservtulcea.ro/img/aqua.png" alt="Aquaserv Tulcea" class="logo-aquaserv" style="height:52px;display:block;margin:0 auto;"/>
        </div>
        <div class="email-body">
            <table>
                @yield('content')
            </table>
        </div>
        <div class="email-footer">
            <p>COPYRIGHT &copy; {{ date('Y') }} <a href="{{ env('APP_URL') }}">MyAqua</a> &middot; Aquaserv Tulcea &middot; Toate drepturile rezervate.</p>
        </div>
    </div>
</div>
</body>
</html>
