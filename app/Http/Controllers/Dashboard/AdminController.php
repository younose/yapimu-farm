<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\AdminDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\Admin\AdminCreateRequest;
use App\Http\Requests\Dashbaord\Admin\AdminUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class AdminController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Admin');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AdminDataTable $dataTable)
    {
        $roles = [
            (object) [
                'name' => 'admin',
                'label' => 'Admin',
            ],
            (object) [
                'name' => 'teller',
                'label' => 'Teller',
            ],
        ];
        $this->setData('roles', $roles);

        return $dataTable
            ->render('dashboard.admin.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->load('roles');

            return (new SuccessResource('Berhasil mengambil data admin', $user))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Admin tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $admin = User::create($validated);
            $admin->markEmailAsVerified();

            $admin->assignRole($validated['role']);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambah admin baru', $admin))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambah admin baru');
        } catch (\Throwable $th) {
            if ($request->ajax() || $request->wantsJson()) {
                return (new ErrorResource($th, 'Terjadi kesalahan!'))
                    ->response()
                    ->setStatusCode(500);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $admin = User::findOrFail($id);
            $admin->update($validated);
            $admin->syncRoles([$validated['role']]);

            return (new SuccessResource('Berhasil mengubah admin', $admin))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Admin tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $admin = User::findOrFail($id);
            $admin->removeRole('investor');
            $activity = Activity::where('subject_id', $id)
                ->where('subject_type', User::class)
                ->get();
            $activity->each->delete();
            $admin->delete();

            return (new SuccessResource('Berhasil menghapus admin', $admin))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Admin tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Admin tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
