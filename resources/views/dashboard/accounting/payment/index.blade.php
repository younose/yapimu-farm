@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1">Jumlah Belum Bayar</p>
                            <h4 class="mb-0"><span class="unpaid-count">0</span></h4>
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
                            <p class="mb-1">Nominal Belum Bayar</p>
                            <h4 class="mb-0"><span class="unpaid-amount">0</span></h4>
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
                    <div class="btn-group mb-2 btn-group-xs">
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
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'payment-table';

        function renderWidget() {
            const {
                unpaidAmount,
                unpaidCount
            } = window.LaravelDataTables[tableName].ajax.json();
            $('.unpaid-count').text(thousandFormat(unpaidCount));
            $('.unpaid-amount').text(idrFormat(unpaidAmount));
        }

        function payZakat(id) {
            let button = $(`#pay-zakat-${id}`);
            let innerButton = button.html();

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: 'Apakah anda yakin ingin melunasi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Bayar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: route('payments.update', id),
                        type: 'put',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            button.prop('disabled', true)
                                .html(spinner);
                        },
                        success: function(response) {
                            if (response.message) {
                                showSuccessToast(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            const response = data.responseJSON;
                            if (response.message) {
                                showErrorToast(response.message);
                            }
                        },
                        complete: function() {
                            button.prop('disabled', false)
                                .html(innerButton);
                            reloadDatatable(tableName);
                        }
                    });
                }
            });
        }
    </script>
@endpush
