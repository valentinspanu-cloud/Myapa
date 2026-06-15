<div id="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->route()->getName() == 'home' ? 'active' : '' }}" href="/">
                    <img src="{{ asset('img/icons/nav/home.svg') }}"
                         alt="@lang('labels.icon') @lang('general.pages.dashboard.home')"/>
                    <span class="nav-link-text">@lang('general.pages.dashboard.home')</span>
                </a>
            </li>
            @if(!isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'index.list' ? 'active' : '' }}"
                       href="{{ route('index.list') }}">
                        <img src="{{ asset('img/icons/nav/index.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.index')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.index')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'invoice.list' ? 'active' : '' }}"
                       href="{{ route('invoice.list') }}">
                        <img src="{{ asset('img/icons/nav/facturi.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.invoices')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.invoices')</span>
                    </a>
                </li>
                {{--<li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'complaints' ? 'active' : '' }}"
                       href="{{ route('complaints') }}">
                        <img src="{{ asset('img/icons/nav/sesizari.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.complaints')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.complaints')</span>
                    </a>
                </li>--}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'notifications' ? 'active' : '' }}"
                       href="{{ route('notification.list') }}">
                        <img src="{{ asset('img/icons/nav/notifications.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.notifications')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.notifications')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'users' ? 'active' : '' }}"
                       href="{{ route('user.account') }}">
                        <img src="{{ asset('img/icons/nav/users.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.my_account')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.my_account')</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'users' ? 'active' : '' }}"
                       href="{{ route('users') }}">
                        <img src="{{ asset('img/icons/nav/multiple-users.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.users')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.users')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'notification.admin' ? 'active' : '' }}"
                       href="{{ route('notification.admin') }}">
                        <img src="{{ asset('img/icons/nav/notifications.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.notifications')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.notifications')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'complaints.admin' ? 'active' : '' }}"
                       href="{{ route('complaints.admin') }}">
                        <img src="{{ asset('img/icons/nav/sesizari.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.complaints')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.complaints')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'cms' ? 'active' : '' }}"
                       href="{{ route('cms') }}">
                        <img src="{{ asset('img/icons/nav/cms.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.cms')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.cms')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'settings' ? 'active' : '' }}"
                       href="{{ route('settings.edit') }}">
                        <img src="{{ asset('img/icons/nav/settings.svg') }}"
                             alt="@lang('labels.icon') @lang('general.pages.dashboard.settings')"/>
                        <span class="nav-link-text">@lang('general.pages.dashboard.settings')</span>
                    </a>
                </li>
            @endif

                @if(isAdmin())
                <li class="nav-item">
                    <a class="nav-link" href="/admin/logs">
                        <i class="ti ti-mail" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Mailuri trimise</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/plati">
                        <i class="ti ti-credit-card" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Istoric plati</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/logs-index">
                        <i class="ti ti-droplet" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Log index contor</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'admin.facturi.index' ? 'active' : '' }}"
                       href="{{ route('admin.facturi.index') }}">
                        <i class="ti ti-send" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Trimitere facturi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'admin.extern-clienti.index' ? 'active' : '' }}"
                       href="{{ route('admin.extern-clienti.index') }}">
                        <i class="ti ti-users" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Clienți externi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'admin.portal-clienti.index' ? 'active' : '' }}"
                       href="{{ route('admin.portal-clienti.index') }}">
                        <i class="ti ti-database-export" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Export clienți</span>
                    </a>
                </li>
                @endif
            @if(auth()->user()->hasRole('extern_manager'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'admin.facturi.index' ? 'active' : '' }}"
                       href="{{ route('admin.facturi.index') }}">
                        <i class="ti ti-list" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Facturi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->route()->getName() == 'admin.extern-clienti.index' ? 'active' : '' }}"
                       href="{{ route('admin.extern-clienti.index') }}">
                        <i class="ti ti-users" style="font-size:18px;width:20px;flex-shrink:0;"></i>
                        <span class="nav-link-text">Clienți externi</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    <div class="sidebar-footer-links">
        <div class="sidebar-footer-separator"></div>
        @if(!isAdmin())
        <div class="sidebar-footer-info">
            @if(getPage(4))
                <a href="{{ route('cms.view', getPage(4)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-message"></i>
                    <span>{{ getPage(4)['title'] }}</span>
                </a>
            @endif
            @if(getPage(9))
                <a href="{{ route('cms.view', getPage(9)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-credit-card"></i>
                    <span>{{ getPage(9)['title'] }}</span>
                </a>
            @endif
            @if(getPage(6))
                <a href="{{ route('cms.view', getPage(6)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-shield"></i>
                    <span>{{ getPage(6)['title'] }}</span>
                </a>
            @endif
            @if(getPage(5))
                <a href="{{ route('cms.view', getPage(5)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-file-text"></i>
                    <span>{{ getPage(5)['title'] }}</span>
                </a>
            @endif
            @if(getPage(8))
                <a href="{{ route('cms.view', getPage(8)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-map-pin"></i>
                    <span>{{ getPage(8)['title'] }}</span>
                </a>
            @endif
            @if(getPage(7))
                <a href="{{ route('cms.view', getPage(7)['slug']) }}" class="sidebar-footer-link">
                    <i class="ti ti-cookie"></i>
                    <span>{{ getPage(7)['title'] }}</span>
                </a>
            @endif
            @if(getSetting('email'))
                <a href="mailto:{{ getSetting('email') }}" class="sidebar-footer-link">
                    <i class="ti ti-mail"></i>
                    <span>{{ getSetting('email') }}</span>
                </a>
            @endif
            @if(getSetting('phone'))
                <a href="tel:{{ getSetting('phone') }}" class="sidebar-footer-link">
                    <i class="ti ti-phone"></i>
                    <span>{{ getSetting('phone') }}</span>
                </a>
            @endif
        </div>
        @endif
        <div class="sidebar-footer-copy">
            © {{ date('Y') }} Aquaserv Tulcea
        </div>
    </div>
</div>
