@php
    $bottomNavNames = ['Dashboard', 'Laporan Keuangan', 'Dividen'];
    $bottomNavItems = collect(getNavigations())
        ->filter(fn($item) => in_array($item->name, $bottomNavNames))
        ->flatMap(fn($item) => isset($item->children) ? $item->children : [$item])
        ->values();
    $bottomNavColors = ['#FF6B6B', '#FFA94D', '#9B5DE5', '#00BBF9', '#2EC4B6', '#6BCB77'];

    // keep "Penarikan Dividen" in the middle of the bar
    $highlightName = 'Penarikan Dividen';
    $highlightItem = $bottomNavItems->first(fn($item) => $item->name === $highlightName);

    if ($highlightItem) {
        $rest = $bottomNavItems->reject(fn($item) => $item->name === $highlightName)->values();
        $middleIndex = intdiv($rest->count(), 2);
        $bottomNavItems = $rest->slice(0, $middleIndex)->values()
            ->push($highlightItem)
            ->merge($rest->slice($middleIndex)->values());
    }
@endphp

<nav class="gopay-bottom-nav">
    @foreach ($bottomNavItems as $i => $item)
        <a href="{{ $item->url }}" class="gopay-bottom-nav-item {{ $item->active ? 'is-active' : '' }}"
            aria-label="{{ $item->name }}" title="{{ $item->name }}">
            <span class="gopay-bottom-nav-icon" style="background: {{ $bottomNavColors[$i % count($bottomNavColors)] }}">
                <i class="{{ $item->icon }}"></i>
            </span>
            <span class="gopay-bottom-nav-label">{{ $item->name }}</span>
        </a>
    @endforeach

    <div class="dropup gopay-bottom-nav-item">
        <a href="javascript:void(0)" class="gopay-bottom-nav-account" data-bs-toggle="dropdown" aria-expanded="false"
            aria-label="Akun">
            <span class="gopay-bottom-nav-icon gopay-bottom-nav-avatar">
                <img src="{{ auth()->user()->photo_url }}" alt=""
                    onerror="this.onerror=null;this.src='{{ asset('images/avatar/1.png') }}'">
            </span>
            <span class="gopay-bottom-nav-label">Akun</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                    <i class="fa-solid fa-user text-primary fa-fw"></i>
                    <span class="ms-2">Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="dropdown-item ai-icon">
                    <i class="fa-solid fa-arrow-right-from-bracket text-danger fa-fw"></i>
                    <span class="ms-2">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
