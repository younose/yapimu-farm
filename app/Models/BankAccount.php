<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BankAccount extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'Rekeneing Bank';

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
