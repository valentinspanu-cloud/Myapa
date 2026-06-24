@extends('layouts.app')
@section('title', 'Panou de control')
@section('content')
    <div id="content-main">
        <div class="container-fluid dashboard-boxes">
            @if (session('message'))
                <div class="row mb-3"><div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show">
                        <span>{{ session('message') }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div></div>
            @endif
            @if (session('status'))
                <div class="row mb-3"><div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show">
                        <span>{{ session('status') }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div></div>
            @endif

            @if(!isAdmin())
                <div class="welcome-bar">
                    <div class="welcome-bar__text">
                        <h2 class="welcome-bar__title">Bună ziua, {{ session('personal')['nume'] ?? auth()->user()->name }}!</h2>
                        <p class="welcome-bar__sub">
                            {{ ucfirst(\Carbon\Carbon::now()->locale('ro')->isoFormat('dddd, D MMMM YYYY')) }}
                            @if(!empty(session('currentLocation')['addr_text']))
                                &nbsp;·&nbsp; {{ session('currentLocation')['addr_text'] }}
                            @endif
                            @if(auth()->user()->codes && count(auth()->user()->codes))
                                &nbsp;·&nbsp; Cod client: {{ auth()->user()->codes[0]['client_code'] }}
                            @endif
                        </p>
                    </div>
                    <div class="welcome-bar__icon">
                        <i class="ti ti-droplet"></i>
                    </div>
                </div>

                @php
                    $unpayed = session('currentLocation')['unPayedInvoices'] ?? [];
                @endphp
                <div class="row mb-4">
                    <div class="col-12 col-md-4">
                        <div class="stat-card stat-card--danger">
                            <div class="stat-card__accent"></div>
                            <div class="stat-card__label">Sold de achitat</div>
                            <div class="stat-card__value stat-card__value--danger">{{ $sold }} RON</div>
                            <div class="stat-card__sub">{{ count($unpayed) }} facturi neachitate</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="stat-card stat-card--blue">
                            <div class="stat-card__accent"></div>
                            <div class="stat-card__label">Locație activă</div>
                            <div class="stat-card__value" style="font-size:13px;margin-top:4px;">{{ session('currentLocation')['addr_text'] ?? '—' }}</div>
                            <div class="stat-card__sub">{{ session('currentLocation')['addr_city'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="stat-card stat-card--teal">
                            <div class="stat-card__accent"></div>
                            <div class="stat-card__label">Notificări necitite</div>
                            <div class="stat-card__value">{{ count(getUnreadNotifications()) }}</div>
                            <div class="stat-card__sub">mesaje noi</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                @if(!isAdmin())
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/index.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.index')</h2>
                            @php
                                $today = now()->day;
                                $luna = now()->locale('ro')->isoFormat('MMMM');
                                $an = now()->year;
                                $inPeriod = $today >= 10 && $today <= 20;
                            @endphp
                            <p class="dashboard-boxes__item__period" style="font-size:11px;color:{{ $inPeriod ? '#16a34a' : '#9ca3af' }};margin-top:4px;text-align:center;font-weight:500;">
                                Perioada de transmitere: 10 - 20 {{ $luna }} {{ $an }}
                                @if($inPeriod)
                                    <span style="display:block;color:#16a34a;font-size:10px;">✓ Perioadă activă</span>
                                @endif
                            </p>
                            <a href="{{ route('index.list') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            @if(count($unpayed))
                                <span class="dashboard-boxes__item__badge">{{ count($unpayed) }} neachitate</span>
                            @endif
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/facturi.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.invoices')</h2>
                            <a href="{{ route('invoice.list') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/notificari.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.notifications')</h2>
                            <a href="{{ route('notification.list') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/user.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.my_account')</h2>
                            <a href="{{ route('user.account') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    @if(getPage(4))
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="bg-white shadow-sm dashboard-boxes__item">
                                <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/contact.svg') }}" alt=""/></div>
                                <h2 class="dashboard-boxes__item__title">{{ getPage(4)['title'] }}</h2>
                                <a href="{{ route('cms.view', getPage(4)['slug']) }}" class="dashboard-boxes__item__link"></a>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon dashboard-boxes__item__icon--globe">
                                <i class="ti ti-world" style="font-size:28px;color:#534AB7;"></i>
                            </div>
                            <h2 class="dashboard-boxes__item__title">Site Aquaserv</h2>
                            <a href="https://aquaservtulcea.ro" target="_blank" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                @else
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/multiple-users_b.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.users')</h2>
                            <a href="{{ route('users') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/notificari.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.notifications')</h2>
                            <a href="{{ route('notification.admin') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/sesizari.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.complaints')</h2>
                            <a href="{{ route('complaints.admin') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/cms.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.cms')</h2>
                            <a href="{{ route('cms') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white shadow-sm dashboard-boxes__item">
                            <div class="dashboard-boxes__item__icon"><img src="{{ asset('img/icons/settings.svg') }}" alt=""/></div>
                            <h2 class="dashboard-boxes__item__title">@lang('general.pages.dashboard.settings')</h2>
                            <a href="{{ route('settings.edit') }}" class="dashboard-boxes__item__link"></a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
