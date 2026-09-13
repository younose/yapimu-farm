@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="btn-group mb-2 btn-group-xs">
                        <button type="button" class="btn btn-xs btn-primary" id="btn-add"
                            onclick="submitForm('#databasebackupForm', onBackupSuccess)">
                            <em class="fas fa-database fa-fw"></em><span>Backup
                            </span>
                        </button>
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
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        const tableName = 'database-backups-table';

        function onBackupSuccess(response) {
            reloadDatatable(tableName);
        }

        function onDeleteSuccess(response) {
            reloadDatatable(tableName);
        }
    </script>
@endpush
