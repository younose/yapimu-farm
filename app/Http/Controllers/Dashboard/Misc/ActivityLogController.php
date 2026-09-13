<?php

namespace App\Http\Controllers\Dashboard\Misc;

use App\DataTables\ActivityLogDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Activity Log');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Lain-lain', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(ActivityLogDataTable $dataTable)
    {
        return $dataTable->render('dashboard.misc.activity.index', $this->data);
    }

    public function show(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $activity->load('causer', 'subject');

            return (new SuccessResource('Berhasil mengambil data activity', $activity))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Activity tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
