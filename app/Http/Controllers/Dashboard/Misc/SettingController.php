<?php

namespace App\Http\Controllers\Dashboard\Misc;

use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Setting\AppSettingUpdateRequest;
use App\Http\Requests\Setting\DatabaseSettingUpdateRequest;
use App\Http\Requests\Setting\DefaultSettingUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Setting');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Lain-lain', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index()
    {
        $groups = Setting::getSettingGroups();
        $this->setData('groups', $groups);

        $settings = Setting::getGrouppedSettings();
        $this->setData('settings', $settings);

        $databaseConnections = Setting::getDatabaseConnections();
        $this->setData('databaseConnections', $databaseConnections);

        return view('dashboard.setting.index', $this->data);
    }

    public function updateApp(AppSettingUpdateRequest $request)
    {
        $validate = $request->validated();
        try {
            DB::beginTransaction();
            foreach ($validate as $key => $value) {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->update(['value' => $value]);
            }
            DB::commit();
            Cache::forget('settings');

            return (new SuccessResource('Pengaturan berhasil disimpan'))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function updateDefault(DefaultSettingUpdateRequest $request)
    {
        $validate = $request->validated();
        try {
            DB::beginTransaction();
            foreach ($validate as $key => $value) {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->update(['value' => $value]);
            }
            DB::commit();
            Cache::forget('settings');

            return (new SuccessResource('Pengaturan berhasil disimpan'))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function updateDatabase(DatabaseSettingUpdateRequest $request)
    {
        $validate = $request->validated();
        try {
            $this->_testDatabse($validate);

            DB::beginTransaction();
            foreach ($validate as $key => $value) {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->update(['value' => $value]);
            }
            DB::commit();
            Cache::forget('settings');

            return (new SuccessResource('Pengaturan berhasil disimpan'))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan! Cek kembali pengaturan database'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function testDatabase(DatabaseSettingUpdateRequest $request)
    {
        $validate = $request->validated();
        try {
            $this->_testDatabse($validate);

            return (new SuccessResource('Koneksi database berhasil'))
                ->response()
                ->setStatusCode(200);

        } catch (\Exception $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan! Cek kembali pengaturan database'))
                ->response()
                ->setStatusCode(500);
        }
    }

    private function _testDatabse($validate)
    {
        // test database connection
        $databaseDriver = $validate['database_driver'];
        $databaseHost = $validate['database_host'];
        $databasePort = $validate['database_port'];
        $databaseDatabase = $validate['database_database'];
        $databaseUsername = $validate['database_username'];
        $databasePassword = $validate['database_password'];

        $cuerrentConnection = config('database.default');
        $currentDatabaseConfig = config("database.connections.{$cuerrentConnection}");

        $connection = [
            'driver' => $databaseDriver,
            'host' => $databaseHost,
            'port' => $databasePort,
            'database' => $databaseDatabase,
            'username' => $databaseUsername,
            'password' => $databasePassword,
        ];

        // test connection
        config(['database.connections.test' => $connection]);
        DB::purge('test');
        $connection = DB::connection('test')->getPdo();
    }
}
