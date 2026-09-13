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
</nav>
