<?php $notifications = getUnreadNotifications(); ?>
<header>
    <div class="topbar-inner">
        <div class="topbar-left">
            <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Meniu">
                <i class="ti ti-menu-2"></i>
            </button>
            <span class="topbar-page-title topbar-page-title--full">Portalul clienților Aquaserv Tulcea</span><span class="topbar-page-title topbar-page-title--short">MyAPA</span>
        </div>
        <div class="topbar-right">
            @if(auth()->user()->hasRole('consumer'))
                <div class="topbar-notif dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">
                        <i class="far fa-bell"></i>
                        @if(count($notifications))
                            <span class="notif-badge">{{ count($notifications) }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h4 class="dropdown-header">@lang('general.pages.dashboard.notifications')</h4>
                        <ul class="notificari">
                            @if(count($notifications))
                                @foreach($notifications as $id => $notification)
                                    @if($id > 4) @continue @endif
                                    <li class="notificare__item">
                                        <div class="notificare__item__content">
                                            <a href="{{ route('notification.view', $notification->id) }}">
                                                <p>{{ $notification->subject }}</p>
                                            </a>
                                            <span class="notificare__item__content__date">{{ $notification->created_at }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                        <a class="secondary-link view-all-alerts-link" href="{{ route('notification.list') }}">
                            Vezi toate notificările
                        </a>
                    </div>
                </div>
            @endif

            <div class="topbar-user">
                <a href="{{ !isAdmin() ? route('user.account') : '#' }}" class="topbar-user__name">
                    {{ session('personal')['nume'] ?? auth()->user()->name }}
                </a>
            </div>

            <a href="{{ route('logout') }}" class="topbar-logout" title="@lang('labels.logout')">
                <i class="fa fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</header>
