@extends('layouts.dashboard')

@section('content')
    @if (auth()->user()->hasRole('investor'))
        <div class="row gopay-hero-row d-block d-md-none">
            <div class="col-12">
                <div class="gopay-hero gopay-hero-compact">
                    <div class="gopay-hero-topbar">
                        <div class="gopay-hero-profile">
                            <span class="gopay-hero-avatar gopay-hero-icon-badge">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                            <div>
                                <div class="gopay-hero-welcome">Laporan Saldo Investor</div>
                                <div class="gopay-hero-name">Riwayat Transaksi Anda</div>
                            </div>
                        </div>
                    </div>

                    <div class="gopay-hero-label">Saldo</div>
                    <div class="gopay-hero-value balance">Rp 0</div>

                    <div class="gopay-hero-stats">
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-arrow-down"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Masuk</div>
                                <div class="gopay-hero-stat-value debit">Rp 0</div>
                            </div>
                        </div>
                        <div class="gopay-hero-stat">
                            <span class="gopay-hero-stat-icon"><i class="fa-solid fa-arrow-up"></i></span>
                            <div class="min-w-0">
                                <div class="gopay-hero-stat-label">Keluar</div>
                                <div class="gopay-hero-stat-value credit">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown w-100">
                        <button type="button" class="gopay-hero-action-btn dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-print"></i>
                            Cetak Laporan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end w-100">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print('excel')">
                                    <i class="fa-solid fa-file-excel text-success fa-fw me-1"></i> Cetak Excel</a>
                            </li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print('pdf')">
                                    <i class="fa-solid fa-file-pdf text-danger fa-fw me-1"></i> Cetak PDF</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row gopay-mobile-hide">
        @hasanyrole('admin|teller')
            <div class="col-12 d-block d-md-none mb-4">
                <select class="w-100 form-select select2" aria-label="Default select example">
                    <option value="null" selected disabled>- Pilih Investor -</option>
                    @foreach ($investors as $item)
                        <option data-photo="{{ $item->photo_url }}" value="{{ $item->id }}">
                            {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        @endhasanyrole
        <div class="col-sm-6 col-md-8">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-user-tag fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Investor</p>
                            <h5 class="mb-0 investor">

                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Lembar Saham</p>
                            <h4 class="mb-0 shares">

                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saldo</p>
                            <h5 class="mb-0 balance"> </h5>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-arrow-down fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Masuk</p>
                            <h5 class="mb-0 debit"> </h5>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-arrow-up fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Keluar</p>
                            <h5 class="mb-0 credit"> </h5>

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
                        @hasanyrole('admin|teller')
                            <div class="col-sm-12 col-md-4 my-2">
                                <select id="investor" class="w-100 form-select select2" aria-label="Default select example">
                                    <option value="null" selected disabled>- Pilih Investor -</option>
                                    @foreach ($investors as $item)
                                        <option data-photo="{{ $item->photo_url }}" value="{{ $item->id }}">
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endhasanyrole
                        <div class="col-sm-12 col-md-8 my-2">
                            <div class="btn-group mb-2 btn-group-xs">
                                @hasanyrole('admin|teller')
                                    <button type="button" class="btn btn-xs btn-danger" onclick="openTransactionModal()">
                                        <i class="fas fa-hand-holding-usd fa-fw"></i>
                                        <span>Tambah Transaksi</span>
                                    </button>
                                @endhasanyrole
                                <div class="btn-group btn-group-xs">
                                    <button type="button" class="btn btn-xs btn-info dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-print fa-fw"></i>
                                        <span>Cetak</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="print('excel')">
                                                <i class="fa-solid fa-file-excel text-success fa-fw me-1"></i> Cetak
                                                Excel</a>
                                        </li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                                onclick="print('pdf')">
                                                <i class="fa-solid fa-file-pdf text-danger fa-fw me-1"></i> Cetak
                                                PDF</a>
                                        </li>
                                    </ul>
                                </div>
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
                    <div class="text-center mt-2 d-none" id="transaction-load-more-wrap">
                        <button type="button" id="transaction-load-more" class="btn btn-light btn-sm">Tampilkan Lebih Banyak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-bs-backdrop="static" id="formModal" tabindex="-1" aria-labelledby="formModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Tambah Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- aler warning mohon berhati-hati dalam menambahakn transaksi --}}
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">Perhatian!</h4>
                        <p>Mohon berhati-hati dalam menambahkan transaksi, pastikan data yang dimasukkan sudah benar dan
                            sesuai.</p>
                    </div>

                    <form id="form" action="{{ route('transactions.store') }}" method="post">
                        @csrf

                        <input type="hidden" name="user_id">

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Transaksi</label>
                            <input required type="text" class="form-control" id="description" name="description"
                                placeholder="Transaksi" minlength="3" maxlength="200">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Jenis Transaksi</label>
                            <select required class="form-select" id="type" name="type">
                                <option value="debit">Debit (Transaksi Masuk)</option>
                                <option value="credit">Credit (Transaksi Keluar)</option>
                            </select>
                        </div>

                        <div class="row align-items-center justify-content-center">
                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Tanggal Transaksi</label>
                                    <input required type="date" class="form-control" id="date" name="date">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-5">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Nominal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" id="amount"
                                            name="amount" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input me-2" id="is_minus" name="is_minus">
                                    <label class="ps-2 form-check" for="is_minus">Nominal Minus</label>
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
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'transaction-table';
        let investorSelects = [];
        let transactionPageLength = 10;
        let transactionLoadMoreInitialized = false;

        function renderWidget() {
            const dt = window.LaravelDataTables[tableName];
            const {
                user,
                debit,
                credit
            } = dt.ajax.json();
            $('.investor').html(user?.name ?? '-');
            $('.shares').html(user?.shares ?? '-');
            $('.balance').html(idrFormat((debit ?? 0) - (credit ?? 0)));
            $('.debit').html(idrFormat(debit ?? 0));
            $('.credit').html(idrFormat(credit ?? 0));

            // mobile investor view: "Tampilkan Lebih Banyak"
            if (document.body.classList.contains('is-wallet-home')) {
                const info = dt.page.info();
                const wrap = document.getElementById('transaction-load-more-wrap');
                const btn = document.getElementById('transaction-load-more');

                if (wrap && btn) {
                    wrap.classList.toggle('d-none', info.recordsDisplay <= info.length);

                    if (!transactionLoadMoreInitialized) {
                        transactionLoadMoreInitialized = true;
                        btn.addEventListener('click', function() {
                            transactionPageLength += 10;
                            dt.page.len(transactionPageLength).draw(false);
                        });
                    }
                }
            }
        }

        function print(format = 'excel') {
            const {
                user
            } = window.LaravelDataTables[tableName].ajax.json();

            const userId = user?.id ?? null;

            if (userId == null) {
                showErrorToast("Silahkan pilih investor terlebih dahulu!");
                return;
            }

            let url = route('transactions.print', {
                id: userId,
                format
            });

            window.open(url, '_blank');
        }

        function openTransactionModal() {
            const investor = $('#investor').val();

            if (investor == null) {
                showErrorToast("Silahkan pilih investor terlebih dahulu!");
                return;
            }

            let form = $('#form');
            form.trigger('reset');
            form.find('input[name="user_id"]').val(investor);

            $('#formModal').modal('show');
        }

        function onSubmitSuccess(response) {
            $('#formModal').modal('hide');
            reloadDatatable(tableName);
        }

        new TomSelect('#investor', {
            'create': false,
            'placeholder': '- Pilih Investor -',
            'onChange': function(value) {
                let url = route('transactions.index', {
                    user_id: value
                });
                window.LaravelDataTables[tableName].settings().ajax.url(url);
                window.LaravelDataTables[tableName].ajax.reload();

                investorSelects.forEach((select) => {
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

                    return `<div class="d-flex align-items-center">
                            <img src="${data.photo}" class="avatar-image avatar-xs rounded-circle" width="30" height="30" alt="..." onerror="this.onerror=null;this.src='{{ asset('images/avatar/1.png') }}'">
                            <span class="ms-2">${data.text}</span>
                        </div>`;
                },
            }
        });
    </script>
@endpush
