<?php

use App\Http\Controllers\Dashboard\Accounting\FoundationJournalsController;
use App\Http\Controllers\Dashboard\Accounting\JaminanJournalsController;
use App\Http\Controllers\Dashboard\Accounting\JournalsController;
use App\Http\Controllers\Dashboard\Accounting\PaymentController;
use App\Http\Controllers\Dashboard\Accounting\PeriodicReportController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\BankAccountController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Dividend\DividendReportController;
use App\Http\Controllers\Dashboard\Dividend\TransactionController;
use App\Http\Controllers\Dashboard\Dividend\WithdrawController;
use App\Http\Controllers\Dashboard\ForumController;
use App\Http\Controllers\Dashboard\InvestorController;
use App\Http\Controllers\Dashboard\Misc\ActivityLogController;
use App\Http\Controllers\Dashboard\Misc\DatabaseBackupController;
use App\Http\Controllers\Dashboard\Misc\SettingController;
use App\Http\Controllers\Dashboard\PeriodsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/manifest.webmanifest', function () {
    return response()->json([
        'name' => config('app.name'),
        'short_name' => config('app.name'),
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#eef2f6',
        'theme_color' => '#2f39a9',
        'icons' => [
            [
                'src' => asset('images/pwa/icon-192.png'),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src' => asset('images/pwa/icon-512.png'),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any',
            ],
        ],
    ])->header('Content-Type', 'application/manifest+json');
})->name('pwa.manifest');

Route::get('/', DashboardController::class)->middleware(['auth', 'verified', 'log.visit'])->name('dashboard');

Route::middleware(['auth', 'log.visit'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['middleware' => ['role:admin|teller']], function () {
        // periods
        Route::resource('periods', PeriodsController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('periods');

        Route::put('/periods/{id}/open', [PeriodsController::class, 'openPeriod'])->name('periods.open');
        Route::post('/periods/{id}/preview', [PeriodsController::class, 'previewClosePeriod'])->name('periods.preview');
        Route::put('/periods/{id}/close', [PeriodsController::class, 'closePeriod'])->name('periods.close');

        // payments
        Route::get('payments/{type}', [PaymentController::class, 'index'])
            ->name('payments.index')
            ->where('type', 'zakat|manajemen|yayasan|koperasi');
        Route::put('payments/{id}', [PaymentController::class, 'update'])
            ->name('payments.update');

        // foundation-journals
        Route::get('/foundation-journals/print', [FoundationJournalsController::class, 'print'])
            ->name('foundation-journals.print');
        Route::resource('foundation-journals', FoundationJournalsController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('foundation-journals');

        // jaminan-journals
        Route::resource('jaminan-journals', JaminanJournalsController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('jaminan-journals');
    });

    Route::group(['middleware' => ['role:admin']], function () {
        // users
        Route::get('/investors/export', [InvestorController::class, 'export'])->name('investors.export');
        Route::resource('investors', InvestorController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('investors');
        Route::resource('admins', AdminController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('admins');

        // misc
        Route::resource('activity-logs', ActivityLogController::class)
            ->only([
                'index',
                'show',
            ])
            ->names('activity-logs');

        Route::resource('database-backups', DatabaseBackupController::class)
            ->only([
                'index',
                'show',
                'store',
                'destroy',
            ])
            ->names('database-backups');

        Route::resource('settings', SettingController::class)
            ->only([
                'index',
            ])
            ->names('settings');
        Route::put('/settings/app', [SettingController::class, 'updateApp'])
            ->name('settings.app.update');
        Route::put('/settings/default', [SettingController::class, 'updateDefault'])
            ->name('settings.default.update');
        Route::put('/settings/database', [SettingController::class, 'updateDatabase'])
            ->name('settings.database.update');
        Route::post('/settings/database/test', [SettingController::class, 'testDatabase'])
            ->name('settings.database.test');

        Route::get('/storage-link', function () {
            Artisan::call('storage:link');

            return 'Storage link created';
        });
    });

    Route::group(['middleware' => ['role:investor']], function () {
        Route::resource('bank-account', BankAccountController::class)
            ->only([
                'index',
                'store',
                'destroy',
                'update',
                'show',
            ])
            ->names('bank-account');
    });

    Route::resource('closings', PeriodicReportController::class)
        ->only([
            'index',
            'show',
        ])
        ->names('closings');
    Route::get('/closings/{id}/print', [PeriodicReportController::class, 'print'])->name('closings.print');
    Route::get('/closings/{id}/print-proof', [PeriodicReportController::class, 'printProof'])->name('closings.print-proof');
    Route::put('/closings/{id}/documents', [PeriodicReportController::class, 'updateDocuments'])
        ->name('closings.documents')
        ->middleware('role:admin|teller');

    Route::resource('dividends', DividendReportController::class)
        ->only([
            'index',
        ])
        ->names('dividends');
    Route::get('/dividends/{id}/print', [DividendReportController::class, 'print'])->name('dividends.print');

    Route::resource('transactions', TransactionController::class)
        ->only([
            'index',
            'store',
        ])
        ->names('transactions');
    Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');

    Route::resource('withdraws', WithdrawController::class)
        ->only([
            'index',
            'store',
            'update',
            'show',
        ])
        ->names('withdraws');
    Route::get('/withdraws/{id}/print', [WithdrawController::class, 'printWithdrawRequest'])->name('withdraws.print');

    Route::resource('journals', JournalsController::class)
        ->only([
            'index',
            'store',
            'destroy',
            'update',
            'show',
        ])
        ->names('journals');

    // forum
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/poll', [ForumController::class, 'poll'])->name('forum.poll');

    // period search (dashboard search box)
    Route::get('/periods/search', [PeriodsController::class, 'search'])->name('periods.search');

});

require __DIR__.'/auth.php';
