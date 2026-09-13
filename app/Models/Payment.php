<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'Pembayaran';

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function getStatuses()
    {
        return (object) [
            'paid' => (object) [
                'bg' => 'bg-primary',
                'label' => 'Dibayar',
                'color' => 'text-white',
                'icon' => 'fa fa-check fa-fw',
            ],
            'unpaid' => (object) [
                'bg' => 'bg-danger',
                'label' => 'Belum Dibayar',
                'color' => 'text-white',
                'icon' => 'fa fa-times fa-fw',
            ],
        ];
    }

    public static function getStatus($status)
    {
        $statuses = self::getStatuses();

        return $statuses->{$status};
    }

    // scopeType
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeZakat($query)
    {
        return $query->type('zakat');
    }

    public function scopeKoperasi($query)
    {
        return $query->type('koperasi');
    }

    public function scopeYayasan($query)
    {
        return $query->type('yayasan');
    }

    public function scopeManajemen($query)
    {
        return $query->type('manajemen');
    }
}
