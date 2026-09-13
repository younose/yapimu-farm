@extends('layouts.dashboard')

@section('content')
    @if (auth()->user()->hasRole('investor'))
        <div class="row gopay-hero-row d-block d-md-none">
            <div class="col-12">
                <div class="gopay-hero gopay-hero-compact">
                    <div class="gopay-hero-topbar">
                        <div class="gopay-hero-profile">
                            <span class="gopay-hero-avatar gopay-hero-period-badge hero-period-badge"
                                style="background: #98A2B3;">
                                <span class="hero-period-number">-</span>
                            </span>
                            <div>
                                <div class="gopay-hero-welcome">Laporan Dividen</div>
                                <div class="gopay-hero-name">
                                    <span class="hero-period-name">Pilih periode</span>
                                    <span class="hero-period-live-wrap"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-label">Dividen Perlembar</div>
                    <div class="gopay-hero-value dividend_nominal">Rp 0</div>

                    <div class="gopay-hero-stats">
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-file-invoice"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Lembar Saham</div>
                                <div class="gopay-hero-stat-value shares_count">0</div>
                            </div>
                        </div>
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Hasil Periode</div>
                                <div class="gopay-hero-stat-value dividend_investor">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-period-picker">
                        <select class="select2" aria-label="Pilih Periode">
                            <option value="null" selected disabled>- Pilih Periode -</option>
                            @foreach ($allPeriods as $item)
                                <option data-data="{{ $item }}" value="{{ $item->id }}">
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="gopay-hero-action-btn" onclick="print()">
                        <i class="fa-solid fa-print"></i>
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="row gopay-mobile-hide">
        {{-- only show on mobile --}}
        <div class="col-12 d-block d-md-none mb-4">
            <select class="w-100 form-select select2" aria-label="Default select example">
                <option value="null" selected disabled>- Pilih Periode -</option>
                @foreach ($allPeriods as $item)
                    <option data-data="{{ $item }}" value="{{ $item->id }}">
                        {{ $item->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-calendar"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Periode</p>
                            <h4 class="mb-0 period">

                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Lembar Saham</p>
                            <h4 class="mb-0 shares_count">
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Hasil Periode</p>
                            <h4 class="mb-0 dividend_investor">
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fas fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Perlembar</p>
                            <h4 class="mb-0 dividend_nominal">
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header gopay-mobile-hide">
                    <div class="row w-100">
                        <div class="col-sm-12 col-md-4 my-2">
                            <select class="w-100 form-select select2" aria-label="Default select example">
                                <option value="null" selected disabled>- Pilih Periode -</option>
                                @foreach ($allPeriods as $item)
                                    <option data-data="{{ $item }}" value="{{ $item->id }}">
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-8 my-2">
                            <div class="btn-group mb-2 btn-group-xs">
                                <button type="button" class="btn btn-xs btn-info" onclick="print()">
                                    <i class="fas fa-print fa-fw"></i>
                                    <span>Cetak</span>
                                </button>
                                <button type="button" class="btn btn-xs btn-secondary"
                                    onclick="reloadDatatable(tableName)">
                                    <i class="fas fa-sync fa-fw"></i>
                                    <span>Reload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{ $dataTable->table() }}
                    </div>
                    <div class="text-center mt-2 d-none" id="dividend-load-more-wrap">
                        <button type="button" id="dividend-load-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        let currentPeriod = @json($currentPeriod);
        const tableName = 'dividend-table';
        let periodeSelects = [];

        let dividendPageLength = 10;
        let dividendLoadMoreInitialized = false;
        const heroPeriodBadgeColors = ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF', '#9B5DE5', '#F15BB5', '#00BBF9', '#FF9F1C', '#2EC4B6'];

        function renderWidget() {
            const dt = window.LaravelDataTables[tableName];
            const {
                // credit,
                // debit,
                // saldo,
                period
            } = dt.ajax.json();

            currentPeriod = period;

            $('.shares_count').html(period?.closing_period?.shares_count ?? 0);
            $('.dividend_investor').html(idrFormat(period?.closing_period?.dividend_investor ?? 0));
            $('.dividend_nominal').html(idrFormat(period?.closing_period?.dividend_nominal ?? 0));

            let periodWidget = '';
            if (period) {
                periodWidget = `<div class="d-flex flex-column">
                    <h4>${period.name}</h4>
                                <small class="text-muted
                                    fs-6">
                                    <small class="badge ${period.status_detail.bg} ${period.status_detail.color}">${period.status_detail.label}</small>
                                    (${period.start_date} - ${period.end_date})
                                </small>
                                </div>`;
            } else {
                periodWidget = `<h6>
                                    <span class="badge bg-danger">Tidak ada periode dibuka</span>
                                </h6>`;
            }

            $('.period').html(periodWidget);

            // keep the mobile hero's period badge/name in sync
            if (period) {
                const periodNumber = (period.name.split(' ')[1]) || '?';
                $('.hero-period-number').text(periodNumber);
                $('.hero-period-badge').css('background', heroPeriodBadgeColors[period.id % heroPeriodBadgeColors.length]);
                $('.hero-period-name').text(period.name);
                $('.hero-period-live-wrap').html(period.status === 'open' ?
                    '<span class="gopay-live-indicator"><span class="gopay-live-dot"></span>Open</span>' :
                    '');
            } else {
                $('.hero-period-number').text('-');
                $('.hero-period-badge').css('background', '#98A2B3');
                $('.hero-period-name').text('Tidak ada periode dibuka');
                $('.hero-period-live-wrap').html('');
            }

            // mobile investor view: "Tampilkan Lebih Banyak"
            if (document.body.classList.contains('is-wallet-home')) {
                const info = dt.page.info();
                const wrap = document.getElementById('dividend-load-more-wrap');
                const btn = document.getElementById('dividend-load-more');

                if (wrap && btn) {
                    wrap.classList.toggle('d-none', info.recordsDisplay <= info.length);

                    if (!dividendLoadMoreInitialized) {
                        dividendLoadMoreInitialized = true;
                        btn.addEventListener('click', function() {
                            dividendPageLength += 10;
                            dt.page.len(dividendPageLength).draw(false);
                        });
                    }
                }
            }
        }

        function print() {
            const periodId = currentPeriod ? currentPeriod.id : 0;
            if (periodId === 0) {
                showErrorToast("Silahkan pilih periode terlebih dahulu!");
                return;
            }
            let url = route('dividends.print', {
                id: periodId
            });

            window.open(url, '_blank');
        }

        document.querySelectorAll('.select2').forEach((el) => {
            periodeSelects.push(
                new TomSelect(el, {
                    'create': false,
                    'placeholder': '- Pilih Periode -',
                    'onChange': function(value) {
                        let url = route('dividends.index', {
                            period_id: value
                        });

                        window.LaravelDataTables[tableName].settings().ajax.url(url);
                        window.LaravelDataTables[tableName].ajax.reload();

                        periodeSelects.forEach((select) => {
                            if (select !== this) {
                                select.setValue(value, true);
                            }
                        });
                    },
                    'render': {
                        'option': function(data, escape) {

                            if (data.value == 'null') {
                                return `<div class="d-flex align-items-center">
                                <span class="ms-2">${data.text}</span>
                                </div>`;
                            }

                            let item = JSON.parse(data.data);
                            return `<div class="d-flex flex-column align-items-start">
                                    <span class="ms-2">${data.text}</span>
                                    <span>
                                        <small class="badge ${item.status_detail.bg} ${item.status_detail.color}">${item.status_detail.label}</small>

                                        (${item.start_date} - ${item.end_date})
                                    </span>
                                </div>`;
                        },
                    }
                }));
        });
    </script>
@endpush
