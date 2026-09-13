<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    const ACTIVITY_LOG_LIMIT = 300;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Activity::created(function () {
            $excess = Activity::count() - self::ACTIVITY_LOG_LIMIT;

            if ($excess > 0) {
                Activity::oldest('id')->limit($excess)->delete();
            }
        });

        // Cache::forget('settings');
        // cchek if connected to database
        if (!DB::connection()->getPdo()) {
            return;
        }

        // has setting table
        if (!DB::connection()->getSchemaBuilder()->hasTable('settings')) {
            return;
        }

        $settings = Cache::rememberForever('settings', function () {
            return \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        });

        $databaseConfig = [];

        foreach ($settings as $key => $value) {
            $key = preg_replace('/_/', '.', $key, 1);
            if (preg_match('/^database\./', $key)) {
                $databaseConfig[preg_replace('/^database\./', '', $key)] = $value;

                continue;
            }
            Config::set($key, $value);
        }

        $currentDatabase = Config::get('database.default');
        $currentDatabaseConfig = Config::get("database.connections.{$currentDatabase}");

        // test connection of $databaseConfig if failed use $currentDatabaseConfig
        try {
            config(['database.connections.test' => $databaseConfig]);
            DB::purge('test');
            $connection = DB::connection('test')->getPdo();
        } catch (\Exception $e) {
            $databaseConfig = $currentDatabaseConfig;
        }

        Config::set("database.connections.{$currentDatabase}", $databaseConfig);
    }
}
