<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use FileTrait, HasFactory, LogsActivity;

    const UPLOAD_PATH = 'settings';

    protected $guarded = [];

    protected static $logName = 'Pengaturan';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function getDatabaseConnections()
    {
        return [
            'mysql' => 'MySQL',
            'pgsql' => 'PostgreSQL',
            'sqlite' => 'SQLite',
            'sqlsrv' => 'SQL Server',
        ];
    }

    public static function getSettingGroups()
    {
        return self::select('group')
            ->distinct()
            ->orderBy('group')
            ->get()
            ->pluck('group');
    }

    public static function getGrouppedSettings()
    {
        return self::orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(function ($group) {
                return $group->mapWithKeys(function ($setting) {
                    return [$setting->key => $setting];
                });
            });
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            $uploadRequest = [
                'app_favicon',
                'app_logo_full',
                'app_logo',
            ];

            $key = $model->key;
            if (request()->hasFile($key) && in_array($key, $uploadRequest)) {
                $oldValue = $model->getOriginal('value');
                $upload = $model->updateFile(self::UPLOAD_PATH, $oldValue, request()->file($key));
                $model->value = $upload;
            }
        });
    }
}
