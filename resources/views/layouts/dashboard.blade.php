<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.head')
</head>

@php
    $isInvestorAuth = auth()->check() && auth()->user()->hasRole('investor');
    $isWalletHome = $isInvestorAuth && request()->routeIs('dashboard', 'journals.index', 'closings.index', 'withdraws.index', 'transactions.index', 'dividends.index', 'forum.index');
@endphp
<body class="{{ $isInvestorAuth ? 'has-bottom-nav' : '' }} {{ $isWalletHome ? 'is-wallet-home' : '' }}">
    <div id="preloader">
        <div class="waviy">
            <span style="--i:1">Y</span>
            <span style="--i:2">A</span>
            <span style="--i:3">P</span>
            <span style="--i:4">I</span>
            <span style="--i:5">M</span>
            <span style="--i:6">U</span>
            <span style="--i:7">&nbsp;</span>
            <span style="--i:8">F</span>
            <span style="--i:9">A</span>
            <span style="--i:10">R</span>
            <span style="--i:11">M</span>
        </div>
    </div>

    <div id="main-wrapper">

        <div class="nav-header">
            <a href="{{ route('dashboard') }}" class="brand-logo ">
                <img src="{{ Storage::url(config('app.logo')) }}" class="mb-3 brand-abbr" alt=""
                    style="
                    max-width: 50px;
                    max-height: 50px;
                    " />

                <img src="{{ Storage::url(config('app.logo_full')) }}" class="mb-3 brand-title" alt=""
                    style="
                    max-width: 150px;
                    max-height: 50px;
                    ">
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                {{ $title }}
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-theme-mode p-0" href="javascript:void(0);">
                                    <i id="icon-light" class="fas fa-sun"></i>
                                    <i id="icon-dark" class="fas fa-moon"></i>
                                </a>
                            </li>
                            {{-- <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link  ai-icon" href="javascript:void(0);" role="button"
                                    data-bs-toggle="dropdown">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12.638 4.9936V2.3C12.638 1.5824 13.2484 1 14.0006 1C14.7513 1 15.3631 1.5824 15.3631 2.3V4.9936C17.3879 5.2718 19.2805 6.1688 20.7438 7.565C22.5329 9.2719 23.5384 11.5872 23.5384 14V18.8932L24.6408 20.9966C25.1681 22.0041 25.1122 23.2001 24.4909 24.1582C23.8709 25.1163 22.774 25.7 21.5941 25.7H15.3631C15.3631 26.4176 14.7513 27 14.0006 27C13.2484 27 12.638 26.4176 12.638 25.7H6.40705C5.22571 25.7 4.12888 25.1163 3.50892 24.1582C2.88759 23.2001 2.83172 22.0041 3.36039 20.9966L4.46268 18.8932V14C4.46268 11.5872 5.46691 9.2719 7.25594 7.565C8.72068 6.1688 10.6119 5.2718 12.638 4.9936ZM14.0006 7.5C12.1924 7.5 10.4607 8.1851 9.18259 9.4045C7.90452 10.6226 7.18779 12.2762 7.18779 14V19.2C7.18779 19.4015 7.13739 19.6004 7.04337 19.7811C7.04337 19.7811 6.43703 20.9381 5.79662 22.1588C5.69171 22.3603 5.70261 22.6008 5.82661 22.7919C5.9506 22.983 6.16996 23.1 6.40705 23.1H21.5941C21.8298 23.1 22.0492 22.983 22.1732 22.7919C22.2972 22.6008 22.3081 22.3603 22.2031 22.1588C21.5627 20.9381 20.9564 19.7811 20.9564 19.7811C20.8624 19.6004 20.8133 19.4015 20.8133 19.2V14C20.8133 12.2762 20.0953 10.6226 18.8172 9.4045C17.5391 8.1851 15.8073 7.5 14.0006 7.5Z"
                                            fill="#4f7086" />
                                    </svg>

                                    @php
                                        $unreadNotifications = auth()->user()->unreadNotifications()->count();
                                    @endphp

                                    @if ($unreadNotifications > 0)
                                        <span class="badge badge-primary badge-pill">{{ $unreadNotifications }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div id="dlab_W_Notification1" class="widget-media dlab-scroll p-3">
                                        <ul class="timeline">
                                            @php
                                                $unreadNotifications = auth()
                                                    ->user()
                                                    ->unreadNotifications()
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(5)
                                                    ->get();
                                            @endphp
                                            @foreach ($unreadNotifications as $item)
                                                @php
                                                    $notificationData = (object) $item->data;
                                                    $notificationMedia = (object) $notificationData->media;
                                                @endphp
                                                <li>
                                                    <a href="#">
                                                        <div class="timeline-panel">
                                                            <div class="media me-2 {{ $notificationMedia->color }}">
                                                                <i class="{{ $notificationMedia->icon }}"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="mb-1">{{ $notificationData->message }}</h6>
                                                                <small
                                                                    class="d-block">{{ date('d M Y H:i:s', strtotime($item->created_at)) }}</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <a class="all-notification text-primary" href="javascript:void(0);">Semua Notifikasi <i class="ti-arrow-right"></i></a>
                                </div>
                            </li> --}}
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        @include('layouts.partials.sidebar')

        <div class="content-body">
            <div class="container-fluid">
                @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                    @include('layouts.partials.breadcrumbs')
                @endif

                @yield('content')
            </div>
        </div>

        <div class="footer">
            <div class="copyright">
                {{-- <p>Copyright © {{ config('app.name') }} {{ date('Y') }}</p> --}}
                <p>Copyright © {{ config('app.name') }} {{ date('Y') }} Developed by <a
                        href="https://mamu.sch.id" target="_blank">TIM IT MAMU</a></p>
            </div>
        </div>
    </div>

    @auth
        @if (auth()->user()->hasRole('investor'))
            @include('layouts.partials.bottom-nav')
        @endif
    @endauth

    <div id="pwa-install-btn" class="pwa-install-btn">
        <i class="fa-solid fa-arrow-down-to-line"></i>
        <span>Install Aplikasi {{ config('app.name') }}</span>
        <button type="button" id="pwa-install-close" class="pwa-install-close">&times;</button>
    </div>

    @include('layouts.partials.foot')
</body>

</html>
