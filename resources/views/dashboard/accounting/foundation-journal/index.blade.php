@extends('layouts.dashboard')

@push('css')
    <link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saldo</p>
                            <h5 class="mb-0"><span class="saldo">0</span></h5>
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
                            <p class="mb-1">Masuk</p>
                            <h5 class="mb-0"><span class="debit">0</span></h5>
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
                            <i class="fas fa-dollar-sign fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Keluar</p>
                            <h5 class="mb-0"><span class="credit">0</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row w-100 align-items-center">
                        <div class="col-sm-12 col-md-4 my-2">
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-calendar"></i>
                                </span>
                                <input class="form-control form-control-sm input-daterange-datepicker" type="text"
                                    name="daterange">
                                {{-- button reset --}}
                                <button type="button" class="btn btn-xs btn-secondary" onclick="resetDate()">
                                    <i class="fas fa-times fa-fw"></i>
                                </button>
                            </div>

                        </div>

                        <div class="col-sm-12 col-md-8 my-2">
                            <div class="btn-group mb-2 btn-group-xs">
                                @hasanyrole('admin|teller')
                                    <button type="button" class="btn btn-xs btn-primary" id="btn-add"
                                        onclick="openAddModel()">
                                        <i class="fas fa-plus fa-fw"></i>
                                        <span>Tambah</span>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-info" onclick="printJournal()">
                                        <i class="fas fa-print fa-fw"></i>
                                        <span>Print</span>
                                    </button>
                                @endhasanyrole
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Laporan Keuangan Yasayan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" action="{{ route('foundation-journals.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
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
@endsection

@push('js')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'journal-table';
        let periodeSelects = [];

        let startDate = null;
        let endDate = null;

        function renderWidget() {
            const {
                credit,
                debit,
                saldo,
            } = window.LaravelDataTables[tableName].ajax.json();

            $('.debit').html(idrFormat(Number(debit)));
            $('.credit').html(idrFormat(Number(credit)));
            $('.saldo').html(idrFormat(Number(saldo)));
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

        function openAddModel() {
            const method = 'post';
            const action = route('foundation-journals.store');
            $('.journal-proof').remove();

            $('#formModalLabel').text('Tambah Laporan Keuangan Yayasan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('foundation-journals.update', id);
            $('.journal-proof').remove();

            $('#formModalLabel').text('Ubah Laporan Keuangan Yayasan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('foundation-journals.show', id);
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
                                `<a href="${proofUrl}" target="_blank" class="journal-proof d-block mt-2">Lihat Bukti Transaksi <i class="fas fa-external-link-alt"></i></a>`
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

        function resetDate() {
            $('input[name="daterange"]').val('');
            let url = route('foundation-journals.index');
            window.LaravelDataTables[tableName].settings().ajax.url(url);
            window.LaravelDataTables[tableName].ajax.reload();
        }

        document.querySelectorAll('.select2').forEach((el) => {
            periodeSelects.push(
                new TomSelect(el, {
                    'create': false,
                    'placeholder': '- Pilih Periode -',
                    'onChange': function(value) {
                        let url = route('foundation-journals.index', {
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

        $('.input-daterange-datepicker').daterangepicker({
            // start date and end date always null
            autoUpdateInput: false,
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD MMMM YYYY',
                applyLabel: 'Pilih',
                cancelLabel: 'Batal',
                daysOfWeek: [
                    "Min",
                    "Sen",
                    "Sel",
                    "Rab",
                    "Kam",
                    "Jum",
                    "Sab"
                ],
                monthNames: [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember"
                ],
                customRangeLabel: "Rentang Tanggal",
            },
            ranges: {
                'Hari ini': [moment(), moment()],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
            },
        });

        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));

            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
        });

        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {
            let start = picker.startDate.format('YYYY-MM-DD');
            let end = picker.endDate.format('YYYY-MM-DD');
            let url = route('foundation-journals.index', {
                start_date: start,
                end_date: end
            });

            startDate = start;
            endDate = end;

            window.LaravelDataTables[tableName].settings().ajax.url(url);
            window.LaravelDataTables[tableName].ajax.reload();
        });

        function printJournal(id) {
            let url = route('foundation-journals.print', {
                'start_date': startDate,
                'end_date': endDate,
            });

            window.open(url, '_blank');
        }
    </script>
@endpush
