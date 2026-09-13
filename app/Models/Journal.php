<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Journal extends Model
{
    use FileTrait, HasFactory, LogsActivity;

    const UPLOAD_PATH = 'journals';

    protected $guarded = [];

    protected $appends = ['proof_url'];

    protected static $logName = 'Catatan Keuangan';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (request()->hasFile('proof')) {
                $model->proof = $model->uploadFile(self::UPLOAD_PATH, request()->file('proof'));
            }
        });

        static::updating(function ($model) {
            if (request()->hasFile('proof')) {
                $model->proof = $model->updateFile(self::UPLOAD_PATH, $model->proof, request()->file('proof'));
            }
        });

        static::deleting(function ($model) {
            $model->deleteFile(self::UPLOAD_PATH, $model->photo);
        });
    }

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

    public function getProofUrlAttribute()
    {
        return $this->proof ? asset('storage/'.$this->proof) : null;
    }

    public function scopeHasProof($query)
    {
        return $query->whereNotNull('proof');
    }
}
