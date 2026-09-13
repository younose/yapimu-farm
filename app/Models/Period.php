<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Period extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'Periode';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function closingPeriod()
    {
        return $this->hasOne(ClosingPeriod::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function getOpenPeriod()
    {
        $open = Period::where('status', 'open')->first();

        if (! $open) {
            $open = Period::orderBy('start_date', 'desc')->first();
        }

        return $open;
    }

    public static function getStatuses()
    {
        return (object) [
            'open' => (object) [
                'bg' => 'bg-primary',
                'label' => 'Open',
                'color' => 'text-white',
                'icon' => 'fa fa-check fa-fw',
            ],
            'closed' => (object) [
                'bg' => 'bg-danger',
                'label' => 'Closed',
                'color' => 'text-white',
                'icon' => 'fa fa-times fa-fw',
            ],
        ];
    }

    public static function getStatuse($status)
    {
        $statuses = self::getStatuses();

        return $statuses->{$status};
    }

    public function dividends()
    {
        return $this->hasMany(Dividend::class);
    }

    public function foundationJournals()
    {
        return $this->hasMany(FoundationJournal::class);
    }
}
