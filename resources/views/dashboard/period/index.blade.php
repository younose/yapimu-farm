@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="btn-group mb-2 btn-group-xs">
                        <button type="button" class="btn btn-xs btn-primary" id="btn-add" onclick="openAddModel()">
                            <i class="fas fa-plus fa-fw"></i>
                            <span>Tambah</span>
                        </button>
                        <button type="button" class="btn btn-xs btn-secondary" onclick="reloadDatatable(tableName)">
                            <i class="fas fa-sync fa-fw"></i>
                            <span>Reload</span>
                        </button>
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="period-form" action="{{ route('periods.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="periode" class="form-label">Nama Periode</label>
                            <input required type="text" class="form-control" id="periode" name="name"
                                placeholder="Nama Periode" minlength="3" maxlength="100">
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input required type="date" class="form-control" id="start_date" name="start_date"
                                        placeholder="tanggal mulai periode">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input required type="date" class="form-control" id="end_date" name="end_date"
                                        placeholder="tanggal mulai periode">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="zakat_percentage" class="form-label">Persentase Zakat</label>
                                    <div class="input-group">
                                        <input required type="number" class="form-control" step="0.1" min="0"
                                            max="100" id="zakat_percentage" name="zakat_percentage" value="2.5">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="cooperative_percentage" class="form-label">Persentase Koperasi</label>
                                    <div class="input-group">
                                        <input required type="number" class="form-control" step="0.1" min="0"
                                            max="100" id="cooperative_percentage" name="cooperative_percentage"
                                            value="2">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="management_fee" class="form-label">Potongan Manajemen</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" min="0"
                                            id="management_fee" name="management_fee" value="6500000">
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted">* Persentase Zakat dan Koperasi diisi dalam bentuk desimal, contoh:
                                2.5% = 2.5</small>
                            <small class="text-muted">* Potongan Manajemen diisi dalam bentuk angka, contoh: Rp 6.500.000
                                = 6500000</small>
                            <small class="text-muted">* Potongan diatas akan ditambahkan atau dikalkulasi saat tutup
                                periode</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btn-save" onclick="submitForm('#period-form', onSubmitSuccess)" type="button"
                        class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" data-bs-backdrop="static" id="closePeriodModal" tabindex="-1"
        aria-labelledby="closePeriodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closePeriodModalLabel">Tutup Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        Pastikan Anda sudah memasukkan informasi <b>pendapatan hasil panen (pendapatan kotor), zakat,
                            koperasi dan
                            manajemen</b> dengan benar. Klik <b>preview</b> untuk menampilkan rekap hasil. Jika sudah sesuai
                        Anda dapat menutup periode.
                    </div>

                    <form id="closing-form">
                        @csrf
                        <div class="mb-3">
                            <label for="closing_name" class="form-label">Nama Periode</label>
                            <input disabled type="text" class="form-control" id="closing_name" name="name"
                                placeholder="Nama Periode" minlength="3" maxlength="100">
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="closing_start_date" class="form-label">Tanggal Mulai</label>
                                    <input required type="date" class="form-control" id="closing_start_date"
                                        name="start_date" placeholder="tanggal mulai periode">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="closing_end_date" class="form-label">Tanggal Akhir</label>
                                    <input required type="date" class="form-control" id="closing_end_date"
                                        name="end_date" placeholder="tanggal mulai periode">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label for="closing_revenue" class="form-label">Pendapatan Hasil Panen (Pendapatan
                                        Kotor)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" min="0"
                                            id="closing_revenue" name="revenue">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="closing_zakat_percentage" class="form-label">Persentase Zakat</label>
                                    <div class="input-group">
                                        <input required type="number" class="form-control" step="0.1"
                                            min="0" max="100" id="closing_zakat_percentage"
                                            name="zakat_percentage" value="2.5">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="closing_cooperative_percentage" class="form-label">Persentase
                                        Koperasi</label>
                                    <div class="input-group">
                                        <input required type="number" class="form-control" step="0.1"
                                            min="0" max="100" id="closing_cooperative_percentage"
                                            name="cooperative_percentage" value="2">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4">
                                <div class="mb-3">
                                    <label for="closing_management_fee" class="form-label">Potongan Manajemen</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" min="0"
                                            id="closing_management_fee" name="management_fee" value="6500000">
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted">* Persentase Zakat dan Koperasi diisi dalam bentuk desimal, contoh:
                                2.5% = 2.5</small>
                            <small class="text-muted">* Potongan Manajemen diisi dalam bentuk angka, contoh: Rp 6.500.000
                                = 6500000</small>
                            <small class="text-muted">* Potongan diatas akan ditambahkan atau dikalkulasi saat tutup
                                periode</small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="rhpp_document" class="form-label">Dokumen RHPP</label>
                                    <input type="file" class="form-control" id="rhpp_document"
                                        name="rhpp_document" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="closing_rhpp_nominal" class="form-label">Nominal RHPP</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="number form-control" min="0"
                                            id="closing_rhpp_nominal" name="rhpp_nominal" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="rhpp_transfer_proof" class="form-label">Bukti Transfer Masuk RHPP</label>
                                    <input type="file" class="form-control" id="rhpp_transfer_proof"
                                        name="rhpp_transfer_proof" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="closing_rhpp_transfer_nominal" class="form-label">Nominal Transfer Aktual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="number form-control" min="0"
                                            id="closing_rhpp_transfer_nominal" name="rhpp_transfer_nominal"
                                            value="0">
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted">* Dokumen RHPP dan bukti transfer beserta nominalnya wajib diisi
                                sebelum periode bisa ditutup (format dokumen: jpg, jpeg, png, atau pdf, maks. 5MB)</small>
                            <small class="text-muted">* Selisih antara Nominal RHPP dan Nominal Transfer Aktual akan
                                otomatis tercatat sebagai Jaminan di menu Jaminan</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button onclick="submitForm('#closing-form', onPreviewSuccess)" type="button"
                        class="btn btn-primary" id="btn-preview">Preview</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" data-bs-backdrop="static" id="previewModal" tabindex="-1"
        aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview Tutup Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped" id="preview-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="row mt-4 align-items-center justify-content-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">SISA HASIL PANEN</p>
                                <p class="lh-sm m-0 fs-4"><span class="net_profit">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">MANAJEMEN</p>
                                <p class="lh-sm m-0 fs-4"><span class="management_fee">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">KOPERASI</p>
                                <p class="lh-sm m-0 fs-4"><span class="cooperative">0</span></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">ZAKAT MAL</p>
                                <p class="lh-sm m-0 fs-4"><span class="zakat">0</span></p>
                            </div>
                            <hr>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4"><b>SISA HASIL PANEN</b></p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend">0</span></b></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center justify-content-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4"><b>DIBAGI 2 KANDANG (YAYASAN DAN INVESTASI)</b></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">Kandang Yayasan</p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend_foundations">0</span></b></p>
                            </div>
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <p class="lh-sm m-0 fs-4">Kandang Investasi</p>
                                <p class="lh-sm m-0 fs-4"><b><span class="dividend_investors">0</span></b></p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitForm('#closing-form', onCLosingSuccess)"
                        class="btn btn-primary">Tutup Periode</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        let period = null;
        let preview = null;

        const tableName = 'period-table';

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
            const action = route('periods.store');


            $('#formModalLabel').text('Tambah Periode');
            $('#period-form').attr('action', action);
            $('#period-form').attr('method', method);
            $('#period-form').trigger('reset');
            numberInput();
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('periods.update', id);

            $('#formModalLabel').text('Ubah Periode');
            $('#period-form').attr('action', action);
            $('#period-form').attr('method', method);
            $('#period-form').trigger('reset');

            const showRoute = route('periods.show', id);
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
                            const element = response.data[key];
                            $(`[name="${key}"]`).val(element);
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

        function openPeriode(id) {
            const action = route('periods.open', id);
            const method = 'put';

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda hanya dapat membuka periode yang belum pernah dibuka sebelumnya!",
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya, Buka Periode!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: method,
                        url: action,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            reloadDatatable(tableName);
                            showSuccessToast(response.message);
                        },
                        error: function(xhr, status, error) {
                            const response = xhr.responseJSON;
                            showErrorToast(response.message ??
                                'Terjadi kesalahan saat membuka periode');
                        }
                    });
                }
            });
        }

        function openModalClosePeriod(id) {
            let modal = $('#closePeriodModal');
            let modalBody = modal.find('.modal-body');
            const innerModalBody = modalBody.html();
            let form = $('#closing-form');

            form.trigger('reset');

            const showRoute = route('periods.show', id);

            $.ajax({
                type: "get",
                url: showRoute,
                dataType: "json",
                beforeSend: function() {
                    modalBody.html(spinner);
                    $('#btn-preview').attr('disabled', true);

                    modal.modal('show');
                },
                success: function(response) {
                    period = response.data;

                    modalBody.html(innerModalBody);
                    $('#btn-preview').attr('disabled', false);

                    for (const key in period) {
                        if (Object.hasOwnProperty.call(response.data, key)) {
                            const element = response.data[key];
                            $(`#closing_${key}`).val(element);
                        }
                    }
                    numberInput();

                    const action = route('periods.preview', period.id);
                    const method = 'post';

                    $('#closing-form').attr('action', action);
                    $('#closing-form').attr('method', method);
                },
                error: function(xhr, status, error) {
                    modal.modal('hide');

                    const response = xhr.responseJSON;
                    toastr.error(response?.message ?? 'Terjadi kesalahan saat mengambil data');
                    modalBody.html(innerModalBody);
                    $('#btn-preview').attr('disabled', false);

                }
            });
        }

        function onPreviewSuccess(response) {
            // $('#closePeriodModal').modal('hide');

            const data = response.data;
            preview = data;
            const journals = data.journals;
            const modal = $('#previewModal');
            const modalBody = modal.find('.modal-body');
            const table = modalBody.find('table tbody');

            table.html('');
            journals.forEach((journal, index) => {
                const row = `
                    <tr>
                        <td>${journal.no}</td>
                        <td>${journal.date}</td>
                        <td>${journal.description}</td>
                        <td>${idrFormat(journal.debit)}</td>
                        <td>${idrFormat(journal.credit)}</td>
                        <td>${idrFormat(journal.balance)}</td>
                    </tr>
                `;

                table.append(row);
            });

            const netProfit = data.net_profit ?? 0;
            const managementFee = data.management_fee ?? 0;
            const cooperative = data.cooperative ?? 0;
            const zakat = data.zakat ?? 0;
            const dividend = data.dividend ?? 0;
            const dividendFoundations = data.dividend_foundations ?? 0;
            const dividendInvestors = data.dividend_investors ?? 0;

            modal.find('.net_profit').text(idrFormat(netProfit));
            modal.find('.management_fee').text(idrFormat(managementFee));
            modal.find('.cooperative').text(idrFormat(cooperative));
            modal.find('.zakat').text(idrFormat(zakat));
            modal.find('.dividend').text(idrFormat(dividend));

            modal.find('.dividend_foundations').text(idrFormat(dividendFoundations));
            modal.find('.dividend_investors').text(idrFormat(dividendInvestors));

            const action = route('periods.close', period.id);
            const method = 'put';

            let form = $('#closing-form');
            form.attr('action', action);
            form.attr('method', method);

            modal.modal('show');
        }

        function onCLosingSuccess(response) {
            $('.modal').modal('hide');
            reloadDatatable(tableName);
        }

        $('#previewModal').on('hidden.bs.modal', function(e) {
            const action = route('periods.preview', period.id);
            const method = 'post';

            $('#closing-form').attr('action', action);
            $('#closing-form').attr('method', method);
        })
    </script>
@endpush
