@extends('layouts.dashboard')

@push('css')
    <link href="{{ asset('/vendor/json-viewer/jquery.json-viewer.css') }}" rel="stylesheet">
@endpush

@section('content')
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
                    <form id="databasebackupForm" action="{{ route('database-backups.store') }}" method="post">
                        @csrf
                    </form>
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
    <div class="modal fade" data-bs-backdrop="static" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="json-viewer" class="json-document"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('/vendor/json-viewer/jquery.json-viewer.js') }}"></script>

    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'activity-table';

        function showDetail(id) {
            const showRoute = route('activity-logs.show', id);
            const modalBody = $('#detailModal').find('.modal-body');
            const innerModalBody = modalBody.html();

            $.ajax({
                type: "get",
                url: showRoute,
                dataType: "json",
                beforeSend: function() {
                    modalBody.html(spinner);
                    $('#detailModal').modal('show');
                },
                success: function(response) {
                    const properties = JSON.stringify(response.data.properties);
                    modalBody.html(innerModalBody);

                    $('#json-viewer').jsonViewer(JSON.parse(properties), {
                        collapsed: false,
                        withQuotes: true,
                        withLinks: true,
                    });
                },
                error: function(xhr, status, error) {
                    hideModal();

                    const response = xhr.responseJSON;
                    toastr.error(response?.message ?? 'Terjadi kesalahan saat mengambil data');
                    modalBody.html(innerModalBody);
                }
            });
        }
    </script>
@endpush
