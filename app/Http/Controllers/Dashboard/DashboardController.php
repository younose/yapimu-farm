<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\_core\DashboardController as _coreDashboardController;
use App\Models\ClosingPeriod;
use App\Models\Dividend;
use App\Models\FoundationJournal;
use App\Models\Payment;
use App\Models\Period;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends _coreDashboardController
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle('Dashboard');

    }

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $role = $user->roles()->first();
        $roleName = $role ? Str::lower($role->name) : 'index';

        $openPeriod = Period::getOpenPeriod();

        if (! $openPeriod) {
            $openPeriod = Period::orderBy('start_date', 'desc')->first();
        }

        $this->setData('openPeriod', $openPeriod);

        $this->setData('stockCount', getSetting('default_share_count'));
        $this->setData('stockPrice', getSetting('default_share_price'));

        $periods = Period::orderBy('start_date', 'desc')
            ->withSum('journals as debit', 'debit')
            ->withSum('journals as credit', 'credit')
            ->limit(8)
            ->get();

        $this->setData('periods', $periods);

        if ($roleName == 'investor') {
            $this->setData('shares', $user->shares);
            $this->setData('balance', $user->balance);

            $dividens = Dividend::where('user_id', $user->id)
                ->with('period.closingPeriod')
                ->orderByDesc('period_id')
                ->limit(8)
                ->get();

            $dividens = $dividens->sortByDesc(function ($dividend) {
                return $dividend->period->start_date;
            });
            $dividens = $dividens->values();

            $this->setData('dividens', $dividens);

            $allDividens = Dividend::where('user_id', $user->id)
                ->with('period.closingPeriod')
                ->orderByDesc('period_id')
                ->get();

            $this->setData('allDividens', $allDividens);

            $withdraws = Withdraw::where('user_id', $user->id)
                ->orderByDesc('id')
                ->get();

            $this->setData('withdraws', $withdraws);

        } else {
            $investors = User::investors();

            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            });

            $this->setData('stockActive', $investors->sum('shares'));
            $this->setData('adminCount', $admins->count());
            $this->setData('investorCount', $investors->count());
            $unpaidAmount = Payment::zakat()->where('status', 'unpaid')->sum('nominal');
            $this->setData('zakatUnpaidAmount', $unpaidAmount);

            // unpaid scopeKoperasi, scopeYayasan, scopeManajemen
            $unpaidAmount = Payment::koperasi()->where('status', 'unpaid')->sum('nominal');
            $this->setData('koperasiUnpaidAmount', $unpaidAmount);

            $yayasanBalance = FoundationJournal::sumBalance();
            $this->setData('yayasanBalance', $yayasanBalance);

            $unpaidAmount = Payment::manajemen()->where('status', 'unpaid')->sum('nominal');
            $this->setData('manajemenUnpaidAmount', $unpaidAmount);

            $withdrawPending = Withdraw::where('status', 'pending')->count();
            $investorBalance = $investors->sum('balance');
            $this->setData('withdrawPending', $withdrawPending);
            $this->setData('investorBalance', $investorBalance);

            $currrentPeriod = $periods->last();
            $prevPeriod = $periods->count() > 1 ? $periods->get(6) : null;

            $currentDebit = $currrentPeriod ? $currrentPeriod->debit : 0;
            $currentCredit = $currrentPeriod ? $currrentPeriod->credit : 0;
            $prevDebit = $prevPeriod ? $prevPeriod->debit : 0;
            $prevCredit = $prevPeriod ? $prevPeriod->credit : 0;

            $debitDiff = $currentDebit - $prevDebit;
            $creditDiff = $currentCredit - $prevCredit;

            $debitPercentage = number_format($debitDiff ? ($debitDiff / ($currentDebit + $prevDebit)) * 100 : 0, 1);
            $creditPercentage = number_format($creditDiff ? ($creditDiff / ($currentCredit + $prevCredit)) * 100 : 0, 1);

            $this->setData('currentDebit', $currentDebit);
            $this->setData('currentCredit', $currentCredit);

            $this->setData('debitPercentage', $debitPercentage);
            $this->setData('creditPercentage', $creditPercentage);

            $closingPeriods = ClosingPeriod::with('period')
                ->orderByDesc('period_id')
                ->get();
            $this->setData('closingPeriods', $closingPeriods);

            $latestClosingPeriod = $closingPeriods->first();
            $this->setData('latestClosingPeriod', $latestClosingPeriod);
            $topInvestors = collect();

            if ($latestClosingPeriod) {
                $topInvestors = Dividend::with('user')
                    ->where('period_id', $latestClosingPeriod->period_id)
                    ->orderByDesc('shares')
                    ->get();
            }

            $this->setData('topInvestors', $topInvestors);
        }

        $view = $roleName == 'investor' ? 'dashboard.investor' : 'dashboard.admin';

        return view($view, $this->data);
    }
}
