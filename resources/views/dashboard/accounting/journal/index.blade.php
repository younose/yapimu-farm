@extends('layouts.dashboard')

@section('content')
    @if (auth()->user()->hasRole('investor'))
        <div class="row gopay-hero-row d-block d-md-none">
            <div class="col-12">
                <div class="gopay-hero gopay-hero-compact">
                    @php
                        $periodBadgeColors = ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF', '#9B5DE5', '#F15BB5', '#00BBF9', '#FF9F1C', '#2EC4B6'];
                        $periodNumber = $currentPeriod ? (explode(' ', $currentPeriod->name)[1] ?? '?') : '-';
                        $periodBadgeColor = $currentPeriod ? $periodBadgeColors[$currentPeriod->id % count($periodBadgeColors)] : '#98A2B3';
                    @endphp
                    <div class="gopay-hero-topbar">
                        <div class="gopay-hero-profile">
                            <span class="gopay-hero-avatar gopay-hero-period-badge hero-period-badge" style="background: {{ $periodBadgeColor }};">
                                <span class="hero-period-number">{{ $periodNumber }}</span>
                            </span>
                            <div>
                                <div class="gopay-hero-welcome">Laporan Catatan Keuangan</div>
                                <div class="gopay-hero-name">
                                    <span class="hero-period-name">{{ $currentPeriod->name ?? 'Tidak ada periode dibuka' }}</span>
                                    <span class="hero-period-live-wrap">
                                        @if ($currentPeriod && $currentPeriod->status === 'open')
                                            <span class="gopay-live-indicator">
                                                <span class="gopay-live-dot"></span>
                                                Open
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-label">Total Saldo Periode <span class="hero-saldo-period-number">{{ $periodNumber }}</span></div>
                    <div class="gopay-hero-value saldo">Rp 0</div>

                    <div class="gopay-hero-period-picker">
                        <select class="select2" aria-label="Pilih Periode">
                            <option value="null" selected disabled>- Pilih Periode -</option>
                            @foreach ($allPeriods as $item)
                                <option data-data="{{ $item }}" value="{{ $item->id }}">
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row gopay-mobile-hide">
        @unless (auth()->user()->hasRole('investor'))
            <div class="col-12 d-block d-md-none mb-4">
                <select class="w-100 form-select select2" aria-label="Default select example">
                    <option value="null" selected disabled>- Pilih Periode -</option>
                    @foreach ($allPeriods as $item)
                        <option data-data="{{ $item }}" value="{{ $item->id }}">
                            {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        @endunless

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
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saldo</p>
                            <h4 class="mb-0"><span class="saldo">0</span></h4>
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
                            <p class="mb-1">Masuk</p>
                            <h4 class="mb-0"><span class="debit">0</span></h4>
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
                            <p class="mb-1">Keluar</p>
                            <h4 class="mb-0"><span class="credit">0</span></h4>
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
                                @hasanyrole('admin|teller')
                                    <button type="button" class="btn btn-xs btn-primary" id="btn-add"
                                        onclick="openAddModel()">
                                        <i class="fas fa-plus fa-fw"></i>
                                        <span>Tambah</span>
                                    </button>
                                @endhasanyrole
                                {{-- <button type="button" class="btn btn-xs btn-info" onclick="reloadDatatable(tableName)">
                                    <i class="fas fa-print fa-fw"></i>
                                    <span>Cetak</span>
                                </button> --}}
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" data-bs-backdrop="static" id="formModal" tabindex="-1" aria-labelledby="formModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Tambah Laporan Keuangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" action="{{ route('journals.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="period_id" class="form-label">Periode</label>
                                    <select required class="select form-select-lg form-select" id="period_id"
                                        name="period_id">
                                        <option value="" selected disabled>Pilih Periode</option>
                                        @foreach ($periods as $period)
                                            @if ($openPeriod)
                                                <option value="{{ $period->id }}"
                                                    {{ $period->id == $currentPeriod->id ? 'selected' : '' }}>
                                                    {{ $period->name }}</option>
                                            @else
                                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Tanggal</label>
                                    <input required type="date" class="form-control" id="date" name="date"
                                        placeholder="Tanggal Transaksi">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea required class="form-control" id="description" name="description" placeholder="Deskripsi" minlength="3"
                                maxlength="100"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="proof" class="form-label">Unggah Bukti Nota</label>
                            <input type="file" class="form-control" id="proof" name="proof" accept="image/*">
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="debit" class="form-label">Masuk</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" id="debit"
                                            name="debit" placeholder="Masuk" min="0" value="0">
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="credit" class="form-label">Keluar</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" id="credit"
                                            name="credit" placeholder="Keluar" min="0" value="0">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btn-save" onclick="submitForm('#form', onSubmitSuccess)" type="button"
                        class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bukti -->
    <div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proofModalLabel">Bukti Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Bukti Transaksi" class="img-fluid" id="proofModalImage">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const periods = @json($periods);
        const tableName = 'journal-table';
        let periodeSelects = [];
        let allJournalsLoaded = false;

        function renderWidget() {
            const dt = window.LaravelDataTables[tableName];
            const {
                credit,
                debit,
                saldo,
                period
            } = dt.ajax.json();

            $('.debit').html(idrFormat(Number(debit)));
            $('.credit').html(idrFormat(Number(credit)));

            $('.saldo').html(idrFormat(Number(saldo)));

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

            // keep the mobile hero's period badge/name in sync with the currently viewed period
            const heroPeriodBadgeColors = ['#FF6B6B', '#FFA94D', '#FFD93D', '#6BCB77', '#4D96FF', '#9B5DE5', '#F15BB5', '#00BBF9', '#FF9F1C', '#2EC4B6'];

            if (period) {
                const periodNumber = (period.name.split(' ')[1]) || '?';
                $('.hero-period-number').text(periodNumber);
                $('.hero-saldo-period-number').text(periodNumber);
                $('.hero-period-badge').css('background', heroPeriodBadgeColors[period.id % heroPeriodBadgeColors.length]);
                $('.hero-period-name').text(period.name);
                $('.hero-period-live-wrap').html(period.status === 'open' ?
                    '<span class="gopay-live-indicator"><span class="gopay-live-dot"></span>Open</span>' :
                    '');
            } else {
                $('.hero-period-number').text('-');
                $('.hero-saldo-period-number').text('-');
                $('.hero-period-badge').css('background', '#98A2B3');
                $('.hero-period-name').text('Tidak ada periode dibuka');
                $('.hero-period-live-wrap').html('');
            }

            // mobile investor view: load every row at once, no "show more" pagination
            if (document.body.classList.contains('is-wallet-home') && !allJournalsLoaded) {
                allJournalsLoaded = true;
                dt.page.len(-1).draw(false);
            }
        }

        function onSubmitSuccess(response) {
            hideModal();
            reloadDatatable(tableName);
        }

        function onDeleteSuccess(response) {
            reloadDatatable(tableName);
        }

        function hideModal() {
            $('#formModal').modal('hide');
        }

        function showProof(url) {
            $('#proofModalImage').attr('src', url);
            $('#proofModal').modal('show');
        }

        function openAddModel() {
            const method = 'post';
            const action = route('journals.store');
            $('.journal-proof').remove();

            $('#formModalLabel').text('Tambah Laporan Keuangan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('journals.update', id);
            $('.journal-proof').remove();

            $('#formModalLabel').text('Ubah Laporan Keuangan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('journals.show', id);
            const modalBody = $('#formModal').find('.modal-body');
            const innerModalBody = modalBody.html();

            $.ajax({
                type: "get",
                url: showRoute,
                dataType: "json",
                beforeSend: function() {
                    modalBody.html(spinner);
                    $('#btn-save').attr('disabled', true);

                    $('#formModal').modal('show');
                },
                success: function(response) {
                    modalBody.html(innerModalBody);
                    $('#btn-save').attr('disabled', false);

                    for (const key in response.data) {
                        if (Object.hasOwnProperty.call(response.data, key)) {
                            if (key != 'proof' && key != 'proof_url') {
                                const element = response.data[key];
                                $(`[name="${key}"]`).val(element);
                            }
                        }
                    }

                    // if proof exists
                    if (response.data.proof) {
                        const proofUrl = response.data.proof_url;
                        if (proofUrl) {
                            $(`[name="proof"]`).after(
                                `<button type="button" onclick="showProof('${proofUrl}')" class="journal-proof btn btn-link p-0 d-block mt-2">Lihat Bukti Transaksi <i class="fas fa-image"></i></button>`
                            );
                        }

                    }

                    numberInput();
                },
                error: function(xhr, status, error) {
                    hideModal();

                    const response = xhr.responseJSON;
                    toastr.error(response?.message ?? 'Terjadi kesalahan saat mengambil data');
                    modalBody.html(innerModalBody);
                    $('#btn-save').attr('disabled', false);

                }
            });
        }

        document.querySelectorAll('.select2').forEach((el) => {
            periodeSelects.push(
                new TomSelect(el, {
                    'create': false,
                    'placeholder': '- Pilih Periode -',
                    'dropdownParent': 'body',
                    'onChange': function(value) {
                        let url = route('journals.index', {
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
                })
            );
        });
    </script>
@endpush
