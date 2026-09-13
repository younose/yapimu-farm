<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use FileTrait, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes;

    const UPLOAD_PATH = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'shares',
        'balance',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static $logName = 'Pengguna';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'photo_url',
    ];

    // before creating upload file
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->password = Hash::make(request()->password);

            if (request()->hasFile('photo')) {
                $model->photo = $model->uploadFile(self::UPLOAD_PATH, request()->file('photo'));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('password')) {
                if (request()->password && (request()->password != '' && request()->password != null)) {
                    $model->password = Hash::make(request()->password);
                } else {
                    $model->password = $model->getOriginal('password');
                }
            }

            if (request()->hasFile('photo')) {
                $model->photo = $model->updateFile(self::UPLOAD_PATH, $model->photo, request()->file('photo'));
            }
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->deleteFile(self::UPLOAD_PATH, $model->photo);
            }
        });
    }

    /**
     * Get the bank accounts for the user.
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get the dividends for the user.
     */
    public function dividends()
    {
        return $this->hasMany(Dividend::class);
    }

    /**
     * Get the withdraws for the user.
     */
    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPhotoUrlAttribute()
    {
        $default = asset('images/avatar/1.png');

        return $this->photo ? $this->getFile($this->photo) : $default;
    }

    public function scopeInvestors($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'investor');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::$logName)
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function getInvestorBalances()
    {
        return User::investors()->sum('balance');
    }
}
