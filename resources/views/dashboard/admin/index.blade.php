@extends('layouts.dashboard')

@section('content')
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
                    <h5 class="modal-title" id="formModalLabel">Tambah Admin</h5>
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
                            <div class="col-sm-23 col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="255"
                                        required>
                                </div>

                            </div>
                            <div class="col-sm-23 col-md-3">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select form-lg mb-3" id="role" name="role" required>
                                        <option value="" selected disabled>Pilih Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->label }}</option>
                                        @endforeach
                                    </select>
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
                                    <input type="email" class="form-control" id="email" name="email" maxlength="255"
                                        required>
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
        const tableName = 'admin-table';

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
            const action = route('admins.store');

            $('[name="password"]').val('password123');
            $('#formModalLabel').text('Tambah Admin');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');
            $('[name="username"]').attr('readonly', false);
            $('[name="email"]').attr('readonly', false);
            $('#formModal').modal('show');
        }

        function openEditModel(id) {
            const method = 'put';
            const action = route('admins.update', id);

            $('#formModalLabel').text('Ubah Admin');
            $('#form').attr('action', action);
            $('#form').attr('method', method);
            $('#form').trigger('reset');

            const showRoute = route('admins.show', id);
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

                    if (response.data.roles) {
                        let roleNames = response.data.roles.map(role => role.name);
                        $('[name="role"]').val(roleNames[0]);
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
