<?php

namespace App\Http\Controllers\Dashboard\Misc;

use App\DataTables\DatabaseBackupDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DatabaseBackupController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Database Backup');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Lain-lain', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(DatabaseBackupDataTable $dataTable)
    {
        return $dataTable->render('dashboard.misc.database-backup.index', $this->data);
    }

    public function show($databaseBackup)
    {
        try {
            return response()->download(storage_path('app/snapshots/'.$databaseBackup.'.sql'));
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        try {
            $filename = date('YmdHis');
            Artisan::call('snapshot:create', [
                'name' => $filename,
            ]);

            if (request()->expectsJson()) {
                return (new SuccessResource('Berhasil backup database'))
                    ->response()
                    ->setStatusCode(200);
            }

            return redirect()->back()->with('success', 'Database Backup Created');
        } catch (\Throwable $th) {

            if (request()->expectsJson()) {
                return (new ErrorResource($th, 'Terjadi Kesalahan'))
                    ->response()
                    ->setStatusCode(500);
            }
            abort(500, $th->getMessage());
        }
    }

    public function destroy($databaseBackup)
    {
        try {
            Artisan::call('snapshot:delete', [
                'name' => $databaseBackup,
            ]);

            return (new SuccessResource('Berhasil menghapus backup database'))
                ->response()
                ->setStatusCode(200);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
