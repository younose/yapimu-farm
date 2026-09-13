@extends('layouts.dashboard')

@section('content')
    @php
        $unreadNotifications = auth()->user()->unreadNotifications()->orderBy('created_at', 'desc')->limit(5)->get();
    @endphp

    <div class="row gopay-hero-row">
        <div class="col-12">
            <div class="gopay-hero">
                <div class="gopay-hero-topbar">
                    <div class="gopay-hero-profile">
                        <img src="{{ auth()->user()->photo_url }}" class="gopay-hero-avatar" alt=""
                            onerror="this.onerror=null;this.src='{{ asset('images/avatar/1.png') }}'">
                        <div>
                            <div class="gopay-hero-welcome">Selamat Datang 👋</div>
                            <div class="gopay-hero-name">{{ auth()->user()->name }}</div>
                        </div>
                    </div>
                    <div class="gopay-hero-icons">
                        <button type="button" class="gopay-icon-btn" data-bs-toggle="modal" data-bs-target="#periodSearchModal">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <div class="gopay-icon-btn-wrap">
                            <button type="button" class="gopay-icon-btn" id="gopay-notif-btn">
                                <i class="fa-solid fa-bell"></i>
                                @if ($unreadNotifications->count() > 0)
                                    <span class="gopay-icon-badge">{{ $unreadNotifications->count() }}</span>
                                @endif
                            </button>
                            <div class="gopay-notif-dropdown" id="gopay-notif-dropdown">
                                @forelse ($unreadNotifications as $item)
                                    @php
                                        $notificationData = (object) $item->data;
                                        $notificationMedia = (object) ($notificationData->media ?? []);
                                    @endphp
                                    <div class="gopay-notif-item">
                                        <span class="gopay-notif-icon"><i class="{{ $notificationMedia->icon ?? 'fa-solid fa-bell' }}"></i></span>
                                        <div>
                                            <div class="gopay-notif-message">{{ $notificationData->message ?? '' }}</div>
                                            <div class="gopay-notif-time">{{ $item->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="gopay-notif-empty">Belum ada notifikasi</div>
                                @endforelse
                            </div>
                        </div>
                        <a href="{{ route('forum.index') }}" class="gopay-icon-btn">
                            <i class="fa-solid fa-comments"></i>
                        </a>
                    </div>
                </div>

                <div class="gopay-hero-label">Total Saldo</div>
                <div class="gopay-hero-value-row">
                    <div class="gopay-hero-value balance" id="balance-value" data-value="{{ idrFormat($balance) }}" data-masked="Rp &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">{{ idrFormat($balance) }}</div>
                    <button type="button" id="balance-toggle" class="gopay-eye-btn" aria-label="Sembunyikan saldo">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div class="gopay-hero-stats">
                    <div class="gopay-hero-stat">
                        <span class="gopay-hero-stat-icon"><i class="fa-solid fa-calendar-check"></i></span>
                        <div class="min-w-0">
                            <div class="gopay-hero-stat-label">Periode Aktif</div>
                            <div class="gopay-hero-stat-value">
                                {{ $openPeriod->name ?? 'Tidak ada' }}
                                @if ($openPeriod && $openPeriod->status === 'open')
                                    <span class="gopay-live-indicator">
                                        <span class="gopay-live-dot"></span>
                                        Open
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="gopay-hero-stat">
                        <span class="gopay-hero-stat-icon"><i class="fa-solid fa-file-invoice"></i></span>
                        <div class="min-w-0">
                            <div class="gopay-hero-stat-label">Total Lembar Saham</div>
                            <div class="gopay-hero-stat-value shares">{{ $shares }} Lembar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="periodSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cari Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="period-search-input" class="form-control mb-3" placeholder="Ketik nama periode...">
                    <div id="period-search-results"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gopay-fullbleed-row gopay-dividend-row">
        <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Pembagian Dividen Anda</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dividend-history-table" class="table table-borderless dividend-list-table">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Dividen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $periodBadgeColors = ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF', '#9B5DE5', '#F15BB5', '#00BBF9', '#FF9F1C', '#2EC4B6'];
                                @endphp
                                @if ($openPeriod && $openPeriod->status === 'open' && ! $allDividens->contains('period_id', $openPeriod->id))
                                    @php
                                        $openPeriodNumber = explode(' ', $openPeriod->name)[1] ?? '?';
                                        $openBadgeColor = $periodBadgeColors[$openPeriod->id % count($periodBadgeColors)];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="dividend-row-left">
                                                <span class="dividend-row-icon is-processing" style="background: {{ $openBadgeColor }}">
                                                    {{ $openPeriodNumber }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="dividend-row-name is-processing">{{ $openPeriod->name }}</div>
                                                    <div class="dividend-row-sub is-processing">Sedang berlangsung budidaya</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dividend-row-right">
                                                <div class="dividend-row-status is-neutral is-processing">Berjalan</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @forelse ($allDividens as $dividen)
                                    @php
                                        $netProfit = optional($dividen->period->closingPeriod)->net_profit;
                                        $trend = is_null($netProfit) ? 'is-neutral' : ($netProfit >= 0 ? 'is-up' : 'is-down');
                                        $trendLabel = is_null($netProfit) ? '-' : ($netProfit >= 0 ? 'Untung' : 'Rugi');
                                        $periodNumber = explode(' ', $dividen->period->name)[1] ?? '?';
                                        $badgeColor = $periodBadgeColors[$dividen->period_id % count($periodBadgeColors)];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="dividend-row-left">
                                                <span class="dividend-row-icon" style="background: {{ $badgeColor }}">
                                                    {{ $periodNumber }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="dividend-row-name">{{ $dividen->period->name }}</div>
                                                    <div class="dividend-row-sub">Dividen perlembar : {{ idrFormat($dividen->dividend) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dividend-row-right">
                                                <div class="dividend-row-amount">{{ idrFormat($dividen->total_dividend) }}</div>
                                                <div class="dividend-row-status {{ $trend }}">{{ $trendLabel }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada data dividen</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($allDividens->count() > 10)
                        <div class="text-center mt-2">
                            <button type="button" id="dividend-show-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row gopay-fullbleed-row">
        <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Penarikan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="withdraw-history-table" class="table table-borderless dividend-list-table">
                            <thead>
                                <tr>
                                    <th>Penarikan</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $withdrawStatusMap = [
                                        'pending' => ['color' => '#FFD93D', 'icon' => 'fa-clock', 'trend' => 'is-neutral'],
                                        'approved' => ['color' => '#6BCB77', 'icon' => 'fa-check', 'trend' => 'is-up'],
                                        'rejected' => ['color' => '#FF6B6B', 'icon' => 'fa-xmark', 'trend' => 'is-down'],
                                        'canceled' => ['color' => '#98A2B3', 'icon' => 'fa-ban', 'trend' => 'is-neutral'],
                                    ];
                                @endphp
                                @forelse ($withdraws as $withdraw)
                                    @php
                                        $statusInfo = $withdrawStatusMap[$withdraw->status] ?? $withdrawStatusMap['pending'];
                                        $statusLabel = $withdraw->getStatus()->label;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="dividend-row-left">
                                                <span class="dividend-row-icon" style="background: {{ $statusInfo['color'] }}">
                                                    <i class="fa-solid {{ $statusInfo['icon'] }}"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="dividend-row-name">Penarikan Dana</div>
                                                    <div class="dividend-row-sub">{{ $withdraw->created_at->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dividend-row-right">
                                                <div class="dividend-row-amount">{{ idrFormat($withdraw->amount) }}</div>
                                                <div class="dividend-row-status {{ $statusInfo['trend'] }}">{{ $statusLabel }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Belum ada riwayat penarikan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($withdraws->count() > 10)
                        <div class="text-center mt-2">
                            <button type="button" id="withdraw-show-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row gopay-fullbleed-row">
        <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
            <div id="user-activity" class="card">
                <div class="card-header border-0 pb-0 d-sm-flex d-block">
                    <div>
                        <h4 class="card-title mb-1">Bagi Hasil 8 Periode Terakhir</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div id="dividend"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
    <script>
        const dividens = @json($dividens);
        const dividenCategories = dividens.map(dividen => dividen.period.name);
        const dividenData = dividens.map(dividen => dividen.total_dividend);

        function dividendbar() {
            var options = {
                series: [{
                    name: 'Bagi Hasil',
                    data: dividenData,
                    color: '#2f39a9',
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded',
                    columnWidth: '45%',
                    borderRadius: 5,

                },
            },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: dividenCategories,
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return idrFormat(val);
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return idrFormat(val);
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#dividend"), options);
            chart.render();
        };

        dividendbar();

        function initShowMoreList(tableId, buttonId) {
            const initialCount = 10;
            const step = 5;
            const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
            const showMoreBtn = document.getElementById(buttonId);
            let visibleCount = initialCount;

            function renderVisibility() {
                rows.forEach(function(row, index) {
                    row.style.display = index < visibleCount ? '' : 'none';
                });

                if (showMoreBtn) {
                    showMoreBtn.style.display = visibleCount >= rows.length ? 'none' : '';
                }
            }

            if (rows.length) {
                renderVisibility();
            }

            if (showMoreBtn) {
                showMoreBtn.addEventListener('click', function() {
                    visibleCount += step;
                    renderVisibility();
                });
            }
        }

        initShowMoreList('dividend-history-table', 'dividend-show-more');
        initShowMoreList('withdraw-history-table', 'withdraw-show-more');

        // toggle balance visibility
        const balanceValue = document.getElementById('balance-value');
        const balanceToggle = document.getElementById('balance-toggle');
        let balanceHidden = false;

        balanceToggle.addEventListener('click', function() {
            balanceHidden = !balanceHidden;
            balanceValue.textContent = balanceHidden ? balanceValue.dataset.masked : balanceValue.dataset.value;
            balanceToggle.querySelector('i').className = balanceHidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            balanceToggle.setAttribute('aria-label', balanceHidden ? 'Tampilkan saldo' : 'Sembunyikan saldo');
        });

        // notification dropdown
        const notifBtn = document.getElementById('gopay-notif-btn');
        const notifDropdown = document.getElementById('gopay-notif-dropdown');

        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target)) {
                notifDropdown.classList.remove('show');
            }
        });

        // period search
        let periodSearchTimeout = null;
        const periodSearchInput = document.getElementById('period-search-input');
        const periodSearchResults = document.getElementById('period-search-results');

        function renderPeriodResults(periods) {
            if (!periods.length) {
                periodSearchResults.innerHTML = '<div class="text-center text-muted py-3">Tidak ada periode ditemukan</div>';
                return;
            }

            periodSearchResults.innerHTML = periods.map(function(period) {
                let meta = period.status;

                if (period.net_profit !== null) {
                    meta += ' • Laba Bersih ' + idrFormat(period.net_profit);
                }

                if (period.dividend_nominal !== null) {
                    meta += ' • Dividen/Lembar ' + idrFormat(period.dividend_nominal);
                }

                return '<div class="period-search-result">' +
                    '<div>' +
                    '<div class="period-search-result-name">' + period.name + '</div>' +
                    '<div class="period-search-result-meta">' + meta + '</div>' +
                    '</div>' +
                    '<span class="badge ' + period.status_bg + ' text-white">' + period.status + '</span>' +
                    '</div>';
            }).join('');
        }

        function searchPeriods(query) {
            fetch('{{ route('periods.search') }}?q=' + encodeURIComponent(query), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        renderPeriodResults(res.data);
                    }
                });
        }

        periodSearchInput.addEventListener('input', function() {
            clearTimeout(periodSearchTimeout);
            const value = this.value;
            periodSearchTimeout = setTimeout(function() {
                searchPeriods(value);
            }, 350);
        });

        document.getElementById('periodSearchModal').addEventListener('shown.bs.modal', function() {
            periodSearchInput.value = '';
            periodSearchInput.focus();
            searchPeriods('');
        });
    </script>
@endpush
