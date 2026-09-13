<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Withdraw extends Model
{
    use FileTrait, HasFactory, LogsActivity;

    const UPLOAD_PATH = 'withdraws';

    protected $guarded = [];

    protected $appends = ['proof_url'];

    protected static $logName = 'Penarikan Dividen';

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

            if (request()->has('status') && in_array(request('status'), ['approved', 'rejected', 'canceled'])) {
                $model->processed_by = auth()->user()->id;
                $model->processed_at = now();
            }
        });

        static::deleting(function ($model) {
            $model->deleteFile(self::UPLOAD_PATH, $model->proof);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by')->withTrashed();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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
            'pending' => (object) [
                'bg' => 'bg-warning',
                'label' => 'Menunggu',
                'color' => 'text-white',
                'icon' => 'fa fa-clock fa-fw',
            ],
            'approved' => (object) [
                'bg' => 'bg-success',
                'label' => 'Disetujui',
                'color' => 'text-white',
                'icon' => 'fa fa-check fa-fw',
            ],
            'rejected' => (object) [
                'bg' => 'bg-danger',
                'label' => 'Ditolak',
                'color' => 'text-white',
                'icon' => 'fa fa-times fa-fw',
            ],
            'canceled' => (object) [
                'bg' => 'bg-secondary',
                'label' => 'Dibatalkan',
                'color' => 'text-white',
                'icon' => 'fa fa-times fa-fw',
            ],
        ];
    }

    public function getStatus()
    {
        return $this->getStatuses()->{$this->status};
    }

    public function getProofUrlAttribute()
    {
        return $this->proof ? asset('storage/'.$this->proof) : null;
    }
}
