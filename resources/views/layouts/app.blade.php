<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.31, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ env('APP_NAME') }}</title>

    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/redesign.css') }}"/>
    @yield('header')
    <script> window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token(),]); ?> </script>
</head>
<body class="page-header-fixed page-sidebar-fixed page-sidebar-closed-hide-logo page-content-white">
{{-- @include('cookieConsent::index') --}}
<script>
    var ajaxUrl = '{{ env('APP_URL') }}';
</script>
@include('layouts.partials.topnav')
<main>
    @include('layouts.partials.sidebar')
    <div id="content">
        @yield('content')
        <footer>
            <div class="footer-container container">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <ul class="nav footer--nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">@lang('labels.home')</a>
                            </li>
                            @if(getPage(1))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(1)['slug']) }}">{{ getPage(1)['title'] }}</a>
                                </li>
                            @endif
                            @if(getPage(4))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(4)['slug']) }}">{{ getPage(4)['title'] }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-12 col-lg-4">
                        <ul class="nav footer--nav flex-column">
                            @if(getPage(6))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(6)['slug']) }}">{{ getPage(6)['title'] }}</a>
                                </li>
                            @endif
                            @if(getPage(5))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(5)['slug']) }}">{{ getPage(5)['title'] }}</a>
                                </li>
                            @endif
                            @if(getPage(7))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(7)['slug']) }}">{{ getPage(7)['title'] }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-12 col-lg-4">
                        <ul class="nav footer--nav flex-column">
                            @if(getSetting('phone'))
                                <li class="nav-item">
                                    <span class="nav-link nav-link-w-text">@lang('labels.phone'):
                                        <a href="tel:{{ getSetting('phone') }}">{{ getSetting('phone') }}</a>
                                    </span>
                                </li>
                            @endif
                            @if(getSetting('email'))
                                <li class="nav-item">
                                    <span class="nav-link nav-link-w-text">@lang('labels.email'):
                                        <a href="mailto:{{ getSetting('email') }}">{{ getSetting('email') }}</a>
                                    </span>
                                </li>
                            @endif
                            @if(getPage(9))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(9)['slug']) }}">{{ getPage(9)['title'] }}</a>
                                </li>
                            @endif
                            @if(getPage(8))
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ route('cms.view', getPage(8)['slug']) }}">{{ getPage(8)['title'] }}</a>
                                </li>
                            @endif
                            <img style="max-height: 50px;" src="{{ asset('img/paylogo.jpg') }}"/>
                            <div style="background: #F5F5F6; text-align:center; height: 40px; padding-top: 2px; box-sizing: border-box;">
                                <img style="max-height: 30px;" src="{{ asset('img/bcr.svg') }}"/>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="text-center text-light te py-3">
                            COPYRIGHT © {{ date('Y') }} {{ env('APP_NAME') }}. @lang('labels.all_rights_reserved').
                        </p>
                    </div>
                </div>
            </div>
        </footer>
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('footer')
    </div>
</main>
</body>
</html>
<script>
(function() {
    var btn = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    if (!btn || !sidebar) return;

    // Creeaza overlay
    var overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    btn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-pass').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.getElementById(this.getAttribute('data-target'));
            if (!input) return;
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Anti-autofill pentru campurile de parola
    var passFields = ['old_pass', 'confirm_old_pass', 'password', 'password_confirmation'];
    passFields.forEach(function(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.setAttribute('type', 'text');
        setTimeout(function() {
            el.setAttribute('type', 'password');
            el.value = '';
        }, 100);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var passFields = ['old_pass', 'confirm_old_pass', 'password', 'password_confirmation'];
    passFields.forEach(function(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.value = '';
        el.addEventListener('focus', function() { this.value = ''; });
        el.addEventListener('input', function() {
            // forteaza repaint ca sa dispara textul de fundal
            this.style.backgroundColor = 'transparent';
        });
    });
});
</script>
