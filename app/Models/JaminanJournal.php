<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JaminanJournal extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'Jaminan';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Entries auto-generated from a period's RHPP vs transfer selisih
     * (tied to a period) can't be edited or deleted by hand — they stay
     * in sync with the closing period's nominal fields instead.
     */
    public function isAutomatic(): bool
    {
        return ! is_null($this->period_id);
    }

    public static function sumDebit()
    {
        return self::sum('debit');
    }

    public static function sumCredit()
    {
        return self::sum('credit');
    }

    public static function sumBalance()
    {
        return self::sumDebit() - self::sumCredit();
    }
}
