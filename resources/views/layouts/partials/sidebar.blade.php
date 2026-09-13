<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu mb-4" id="menu">
            <li class="dropdown header-profile">
                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                    <img src="{{ asset(auth()->user()->photo_url) }}" class="avatar-image avatar-sm" width="20" alt=""
                        onerror="this.onerror=null;this.src='{{ asset('images/avatar/1.png') }}'">
                    <div class="header-info ms-3">
                        <span class="font-w600 ">Hi, <b>{{ stringLimit(auth()->user()->name) }}</b></span>
                        <small class="text-end font-w400">{{ stringLimit(auth()->user()->email, 20) }}</small>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
                            height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="ms-2">Profile </span>
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item ai-icon">
                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                            height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="ms-2">Logout </span>
                    </a>
                </div>
            </li>

            @foreach (getNavigations() as $i => $item)
                @if (isset($item->children))
                    <li>
                        <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="{{ $item->icon }}"></i>
                            <span class="nav-text">{{ $item->name }}</span>
                        </a>

                        <ul aria-expanded="false">
                            @foreach ($item->children as $j => $child)
                                <li>
                                    <a href="{{ $child->url }}">{{ $child->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li>
                        <a href="{{ $item->url }}">
                            <i class="{{ $item->icon }}"></i>
                            <span class="nav-text">{{ $item->name }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="copyright d-none">
            <p><strong>{{ config('app.name') }}</strong> © {{ date('Y') }}</p>
            {{-- <p class="fs-12">Made with <span class="heart"></span> by DexignLab</p> --}}
        </div>
    </div>
</div>
