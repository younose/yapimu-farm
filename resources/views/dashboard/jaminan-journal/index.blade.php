@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saldo Jaminan</p>
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
                            <p class="mb-1">Jaminan Masuk</p>
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
                            <p class="mb-1">Jaminan Keluar</p>
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
                    <div class="alert alert-info w-100 mb-0" role="alert">
                        Selisih antara nominal RHPP dan nominal transfer aktual pada <b>Lap. Tutup Periode</b> otomatis
                        tercatat di sini sebagai <b>Jaminan Masuk</b> (ditandai badge "Otomatis"). Anda tetap bisa
                        menambahkan Jaminan Masuk/Keluar lain secara manual.
                    </div>
                </div>
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Jaminan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" action="{{ route('jaminan-journals.store') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal</label>
                            <input required type="date" class="form-control" id="date" name="date"
                                placeholder="Tanggal">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea required class="form-control" id="description" name="description" placeholder="Deskripsi"
                                minlength="3" maxlength="255"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="debit" class="form-label">Jaminan Masuk</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" id="debit"
                                            name="debit" placeholder="Jaminan Masuk" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="credit" class="form-label">Jaminan Keluar</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <input required type="text" class="number form-control" id="credit"
                                            name="credit" placeholder="Jaminan Keluar" min="0" value="0">
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
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'jaminan-journal-table';

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
            const action = route('jaminan-journals.store');

            $('#formModalLabel').text('Tambah Jaminan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            numberInput();
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('jaminan-journals.update', id);

            $('#formModalLabel').text('Ubah Jaminan');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('jaminan-journals.show', id);
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
    </script>
@endpush
