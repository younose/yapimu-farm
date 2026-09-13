<?php

namespace App\Models;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClosingPeriod extends Model
{
    use FileTrait, HasFactory, LogsActivity;

    const UPLOAD_PATH = 'closing_periods';

    protected $guarded = [];

    protected $appends = ['rhpp_document_url', 'rhpp_transfer_proof_url'];

    protected static $logName = 'Tutup Periode';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (request()->hasFile('rhpp_document')) {
                $model->rhpp_document = $model->uploadFile(self::UPLOAD_PATH, request()->file('rhpp_document'));
            }

            if (request()->hasFile('rhpp_transfer_proof')) {
                $model->rhpp_transfer_proof = $model->uploadFile(self::UPLOAD_PATH, request()->file('rhpp_transfer_proof'));
            }
        });

        static::saved(function ($model) {
            $model->syncJaminanEntry();
        });
    }

    /**
     * Keep the "Jaminan" ledger in sync with this period's RHPP vs actual
     * transfer nominal. The selisih is held as collateral ("jaminan") at
     * the PT, so it's recorded automatically instead of by hand. Runs on
     * every save so completing an old period's nominal later (or fixing
     * a typo) keeps the linked jaminan entry correct.
     */
    public function syncJaminanEntry(): void
    {
        if (is_null($this->rhpp_nominal) || is_null($this->rhpp_transfer_nominal)) {
            return;
        }

        $selisih = (float) $this->rhpp_nominal - (float) $this->rhpp_transfer_nominal;

        JaminanJournal::updateOrCreate(
            ['period_id' => $this->period_id],
            [
                'date' => optional($this->period)->end_date ?? now()->toDateString(),
                'description' => 'Selisih RHPP Periode '.(optional($this->period)->name ?? $this->period_id),
                'debit' => max($selisih, 0),
                'credit' => max(-$selisih, 0),
            ]
        );
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

    public function getRhppDocumentUrlAttribute()
    {
        return $this->rhpp_document ? asset('storage/'.$this->rhpp_document) : null;
    }

    public function getRhppTransferProofUrlAttribute()
    {
        return $this->rhpp_transfer_proof ? asset('storage/'.$this->rhpp_transfer_proof) : null;
    }
}
