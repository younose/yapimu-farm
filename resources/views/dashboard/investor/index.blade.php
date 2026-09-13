@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fas fa-users fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Jumlah Investor</p>
                            <h4 class="mb-0 investor-count">3280</h4>
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
                            <i class="fas fa-file fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Lembar Saham</p>
                            <h4 class="mb-0 share-count">3280</h4>
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
                            <i class="fas fa-user-check fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Saham Diakuisisi</p>
                            <h4 class="mb-0 share-aqumulated">3280</h4>
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
                            <p class="mb-1">Harga Perlembar</p>
                            <h5 class="mb-0 share-price">3280</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-hand-holding-dollar fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Total Saldo Investor</p>
                            <h5 class="mb-0"><span class="saldo"></span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-4">
                    <div class="btn-group mb-2 btn-group-xs">
                        <button type="button" class="btn btn-xs btn-primary" id="btn-add" onclick="openAddModel()">
                            <i class="fas fa-plus fa-fw"></i>
                            <span>Tambah</span>
                        </button>
                        <a href="{{route('investors.export')}}" target="_blank" class="btn btn-xs btn-info">
                            <i class="fas fa-print fa-fw"></i>
                            <span>Cetak</span>
                        </a>
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Investor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Perhatian!</strong> Pengguna login menggunakan Email/ Password. Pastikan email/ username
                        yang
                        digunakan unik.
                    </div>
                    <form id="form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="255"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="mb-3">
                                    <label for="shares" class="form-label">Total Saham</label>
                                    <input type="number" class="form-control" id="shares" name="shares" value="0"
                                        min="0" step="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        maxlength="255" required>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        maxlength="255" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3 position-relative">
                                    <label for="dlab-password" class="form-label">Password</label>
                                    <input name="password" type="password" id="dlab-password"
                                        class="form-control form-control" placeholder="Masukkan password" required
                                        value="password123" minlength="8">
                                    <span class="show-pass eye">
                                        <i class="fa fa-eye-slash"></i>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    <small>Secara default password adalah <strong>password123</strong></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto</label>
                                    <input type="file" class="form-control" id="photo" name="photo"
                                        accept="image/*">
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
        const tableName = 'investor-table';

        function renderWidget() {
            console.log('dsd');
            //             share-count
            // share-aqumulated
            // share-price
            const {
                totalShares,
                sharesPrice,
                aqumulatedShares,
                investorBalance,
                investorCount
            } = window.LaravelDataTables[tableName].ajax.json();
            $('.share-count').html(totalShares ?? 0);
            $('.investor-count').html(investorCount ?? 0);
            $('.share-aqumulated').html(aqumulatedShares ?? 0);
            $('.share-price').html(idrFormat(sharesPrice ?? 0));
            $('.saldo').html(idrFormat(investorBalance ?? 0));

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
            const action = route('investors.store');

            $('[name="password"]').val('password123');
            $('#formModalLabel').text('Tambah Investor');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('[name="username"]').attr('readonly', false);
            $('[name="email"]').attr('readonly', false);
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('investors.update', id);

            $('#formModalLabel').text('Ubah Investor');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('investors.show', id);
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

                    $('[name="username"]').attr('readonly', true);
                    $('[name="email"]').attr('readonly', true);

                    $('[name="password"]').val('');
                    $('[name="photo"]').val('');
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
