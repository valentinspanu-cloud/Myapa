<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ env('APP_NAME') }}</title>

    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
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
