@php
    use App\Models\Form;
    use App\Models\Booking;
    $user = \Auth::user();
    $currantLang = $user->currentLanguage();
    $languages = Utility::languages();
    $role_id = $user->roles->first()->id;
    $user_id = $user->id;
    if (Auth::user()->type == 'Admin') {
        $forms = Form::all();
        $all_forms = Form::all();
        $bookings = Booking::all();
    } else {
        $forms = Form::select(['forms.*'])->where(function ($query) use ($role_id, $user_id) {
            $query
                ->whereIn('forms.id', function ($query1) use ($role_id) {
                    $query1
                        ->select('form_id')
                        ->from('assign_forms_roles')
                        ->where('role_id', $role_id);
                })
                ->OrWhereIn('forms.id', function ($query1) use ($user_id) {
                    $query1
                        ->select('form_id')
                        ->from('assign_forms_users')
                        ->where('user_id', $user_id);
                });
        });
        $bookings = Booking::all();
        $all_forms = Form::select('id', 'title')
            ->where('created_by', $user->id)
            ->get();
    }
    $bookings = $bookings->all();
@endphp
<nav class="dash-sidebar light-sidebar {{ $user->transprent_layout == 1 ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="text-center b-brand">
                <!-- ========   change your logo hear   ============ -->
                @if ($user->dark_layout == 1)
                    <img src="{{ Utility::getsettings('app_logo') ? Storage::url('appLogo/app-logo.png') : Storage::url('appLogo/78x78.png') }}"
                        class="app-logo" />
                @else
                    <img src="{{ Utility::getsettings('app_dark_logo') ? Storage::url('appLogo/app-dark-logo.png') : Storage::url('appLogo/78x78.png') }}"
                        class="app-logo" />
                @endif
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar d-block">
                <li class="dash-item dash-hasmenu {{ request()->is('/') ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="dash-link"><span class="dash-micon"><i
                                class="ti ti-home"></i></span>
                        <span class="dash-mtext custom-weight">{{ __('Dashboard') }}</span></a>
                </li>
                @can('manage-dashboardwidget')
                    <li class="dash-item dash-hasmenu {{ request()->is('index-dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('index.dashboard') }}" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-square"></i></span>
                            <span class="dash-mtext custom-weight">{{ __('Dashboard Widgets') }}</span></a>
                    </li>
                @endcan
                @can('manage-blog')
                <li class="dash-item dash-hasmenu {{ request()->is('zoos*') ? 'active' : '' }}">
                    <a href="{{ route('zoos.index') }}" class="dash-link">
                        <span class="dash-micon">
                            <i class="ti ti-map"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Zoos') }}
                        </span>
                    </a>
                </li>
                @endcan
                @canany(['manage-user', 'manage-role'])
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('users*') || request()->is('roles*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-layout-2"></i></span><span
                                class="dash-mtext">{{ __('User Management') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-user')
                                <li class="dash-item {{ request()->is('users*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('users.index') }}">{{ __('Users') }}</a>
                                </li>
                            @endcan
                            @can('manage-role')
                                <li class="dash-item {{ request()->is('roles*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('roles.index') }}">{{ __('Roles') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['manage-blog', 'manage-category'])
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('blogs*') || request()->is('blogcategory*') ? 'active dash-trigger' : 'collapsed' }}">
                        <a href="#!" class="dash-link">
                            <span class="dash-micon">
                                <i class="ti ti-forms"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Blog') }}</span>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i>
                            </span>
                        </a>
                        <ul class="dash-submenu">
                            @can('manage-blog')
                                <li class="dash-item {{ request()->is('blogs*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a>
                                </li>
                            @endcan
                            @can('manage-category')
                                <li class="dash-item {{ request()->is('blogcategory*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('blogcategory.index') }}">{{ __('Categories') }}</a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @canany(['manage-event'])
                    <li class="dash-item dash-hasmenu {{ request()->is('event*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('event.index') }}"><span class="dash-micon">
                                <i class="ti ti-calendar"></i></span>
                            <span class="dash-mtext">{{ __('Event Calender') }}</span>
                        </a>
                    </li>
                @endcanany
                @canany(['manage-chat'])
                    @if (setting('pusher_status') == '1')
                        <li
                            class="dash-item dash-hasmenu {{ request()->is('chat*') ? 'active dash-trigger' : 'collapsed' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-table"></i></span><span
                                    class="dash-mtext">{{ __('Support') }}</span><span class="dash-arrow"><i
                                        data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage-chat')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{ route('chats') }}">{{ __('Chats') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif
                @endcanany
                @canany(['manage-mailtemplate', 'manage-sms-template', 'manage-language', 'manage-setting'])
                    <li
                        class="dash-item dash-hasmenu {{ request()->is('mailtemplate*') || request()->is('sms-template*') || request()->is('manage-language*') || request()->is('create-language*') || request()->is('settings*') ? 'active dash-trigger' : 'collapsed' }} || {{ request()->is('create-language*') || request()->is('settings*') ? 'active' : '' }}">
                        <a href="#!" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-apps"></i></span><span
                                class="dash-mtext">{{ __('Account Setting') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @can('manage-mailtemplate')
                                <li class="dash-item {{ request()->is('mailtemplate*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('mailtemplate.index') }}">{{ __('Email Templates') }}</a>
                                </li>
                            @endcan
                            @can('manage-sms-template')
                                <li class="dash-item {{ request()->is('sms-template*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('sms-template.index') }}">{{ __('Sms Templates') }}</a>
                                </li>
                            @endcan
                            @can('manage-language')
                                <li
                                    class="dash-item {{ request()->is('manage-language*') || request()->is('create-language*') ? 'active' : '' }}">
                                    <a class="dash-link"
                                        href="{{ route('manage.language', [$currantLang]) }}">{{ __('Manage Languages') }}</a>
                                </li>
                            @endcan
                            @can('manage-setting')
                                <li class="dash-item {{ request()->is('settings*') ? 'active' : '' }}">
                                    <a class="dash-link" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

            </ul>
        </div>
    </div>
</nav>
