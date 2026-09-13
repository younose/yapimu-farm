@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fas fa-bank fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Akun Bank</p>
                            <h4 class="mb-0">{{ count($bankAccount) }}</h4>
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
                            <i class="fas fa-bank fa-fw"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Jenis Bank</p>
                            <h4 class="mb-0">{{ count($bankAccount) }}</h4>
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Akun Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" maxlength="255"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Jenis Bank</label>
                            <select class="form-control select2" name="bank_name" id="bank_name" required
                                aria-label="Default select example">
                                <option value="null" selected disabled>- Pilih Bank -</option>
                                @foreach ($typeBank as $bank)
                                    <option value="{{ $bank }}">{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="account_number" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                maxlength="255" required>
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
        const tableName = 'bank-account-table';

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
            const action = route('bank-account.store');

            $('#formModalLabel').text('Tambah Akun Bank');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('bank-account.update', id);

            $('#formModalLabel').text('Ubah Akun Bank');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('bank-account.show', id);
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
