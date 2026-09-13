<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\PeriodDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\Period\CloseClosingPeriodRequest;
use App\Http\Requests\Dashbaord\Period\CreatePeriodRequest;
use App\Http\Requests\Dashbaord\Period\PreviewClosingPeriodRequest;
use App\Http\Requests\Dashbaord\Period\UpdatePeriodRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\ClosingPeriod;
use App\Models\Dividend;
use App\Models\FoundationJournal;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PeriodsController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Periode');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Periode', '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PeriodDataTable $dataTable)
    {
        return $dataTable
            ->render('dashboard.period.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $period = Period::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data periode', $period))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Search periods by name (used by the investor dashboard search box).
     */
    public function search(\Illuminate\Http\Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $periods = Period::with('closingPeriod')
            ->when($query !== '', function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%');
            })
            ->orderByDesc('start_date')
            ->limit(10)
            ->get()
            ->map(function ($period) {
                $status = $period->getStatuse($period->status);

                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'status' => $status->label,
                    'status_bg' => $status->bg,
                    'net_profit' => $period->closingPeriod->net_profit ?? null,
                    'dividend_nominal' => $period->closingPeriod->dividend_nominal ?? null,
                ];
            });

        return (new SuccessResource('Hasil pencarian periode', $periods))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePeriodRequest $request)
    {
        try {
            $validated = $request->validated();
            $period = Period::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil membuat periode baru', $period))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil membuat periode baru');
        } catch (\Throwable $th) {
            if ($request->ajax() || $request->wantsJson()) {
                return (new ErrorResource($th, 'Terjadi kesalahan!'))
                    ->response()
                    ->setStatusCode(500);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeriodRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            // has closing period
            $period = Period::findOrFail($id);
            if ($period->closingPeriod()->exists()) {
                return (new ErrorResource(null, 'Periode tidak bisa diubah karena sudah ada data penutupan periode'))
                    ->response()
                    ->setStatusCode(400);
            }
            $period->update($validated);

            return (new SuccessResource('Berhasil mengubah periode', $period))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $period = Period::findOrFail($id);
            $period->delete();

            return (new SuccessResource('Berhasil menghapus periode', $period))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Periode tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function openPeriod(string $id)
    {
        try {
            DB::beginTransaction();
            $period = Period::findOrFail($id);
            $openingPeriod = Period::where('status', 'open')->first();
            if ($openingPeriod) {
                return (new ErrorResource(null, 'Periode tidak bisa dibuka karena masih ada periode yang terbuka'))
                    ->response()
                    ->setStatusCode(400);
            }

            if ($period->closingPeriod()->exists()) {
                return (new ErrorResource(null, 'Periode tidak bisa dibuka karena sudah ada data penutupan periode'))
                    ->response()
                    ->setStatusCode(400);
            }

            $period->update(['status' => 'open']);

            // $users = User::select('id', 'name', 'email')
            //     ->get();

            // foreach ($users as $user) {
            //     $user->notify(new PeriodOpenedNotification($period));
            // }

            DB::commit();

            return (new SuccessResource('Berhasil membuka periode', $period))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function closePeriod(CloseClosingPeriodRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $period = Period::findOrFail($id);

            $period->update([
                'management_fee'            => $validated['management_fee'],
                'cooperative_percentage'    => $validated['cooperative_percentage'],
                'zakat_percentage'          => $validated['zakat_percentage'],
            ]);

            if ($period->closingPeriod) {
                return (new ErrorResource(null, 'Periode tidak bisa ditutup karena sudah ada data penutupan periode'))
                    ->response()
                    ->setStatusCode(400);
            }

            if ($period->status !== 'open') {
                return (new ErrorResource(null, 'Periode tidak bisa ditutup karena status bukan open'))
                    ->response()
                    ->setStatusCode(400);
            }

            Journal::create([
                'period_id' => $period->id,
                'date' => $period->end_date,
                'description' => 'Pendapatan Hasil Panen',
                'debit' => (float) $validated['revenue'],
                'credit' => 0,
            ]);

            $preview = $this->_getPreview($period, $validated, false);

            $payment = Payment::insert([
                [
                    'period_id' => $period->id,
                    'nominal' => $preview->zakat,
                    'type' => 'zakat',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'period_id' => $period->id,
                    'nominal' => $preview->cooperative,
                    'type' => 'koperasi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'period_id' => $period->id,
                    'nominal' => $preview->management_fee,
                    'type' => 'manajemen',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // [
                //     'period_id' => $period->id,
                //     'nominal' => $preview->dividend_foundations,
                //     'type' => 'yayasan',
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ],
            ]);

            FoundationJournal::create([
                'period_id' => $period->id,
                'date' => $period->end_date,
                'description' => 'Bagi Hasil Periode ' . $period->name,
                'debit' => $preview->dividend_foundations,
                'credit' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $investors = User::investors()
                ->get([
                    'id',
                    'name',
                    'shares',
                    'balance',
                ]);

            $sharePrice = getSetting('default_share_price');
            $shareCount = User::investors()->sum('shares');

            $dividends = [];
            $transactions = [];

            foreach ($investors as $investor) {
                $fund = $investor->shares * $sharePrice;
                $dividend = $preview->dividend_investors / $shareCount;
                $totalDividend = $dividend * $investor->shares;
                $dividends[] = [
                    'period_id' => $period->id,
                    'user_id' => $investor->id,
                    'shares' => $investor->shares,
                    'price_per_share' => $sharePrice,
                    'fund' => $fund,
                    'period_result' => $preview->dividend_investors,
                    'dividend' => $dividend,
                    'total_dividend' => $totalDividend,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $transactions[] = [
                    'user_id' => $investor->id,
                    'reference_id' => $period->id,
                    'description' => sprintf('Bagi Hasil Periode %s (%s - %s)', $period->name, $period->start_date, $period->end_date),
                    'type' => 'debit',
                    'amount' => $totalDividend,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $investor->balance += $totalDividend;
                $investor->save();
            }

            Dividend::insert($dividends);
            Transaction::insert($transactions);

            $closingPeriod = ClosingPeriod::create([
                'period_id' => $period->id,
                'revenue' => $preview->revenue,
                'expenses' => $preview->expenses,
                'net_profit' => $preview->net_profit,
                'management_fee' => $preview->management_fee,
                'cooperative_fee' => $preview->cooperative,
                'zakat' => $preview->zakat,
                'dividend' => $preview->dividend,
                'dividend_foundation' => $preview->dividend_foundations,
                'dividend_investor' => $preview->dividend_investors,
                'shares_count' => $shareCount,
                'dividend_nominal' => $preview->dividend_investors / $shareCount,
                'rhpp_nominal' => $validated['rhpp_nominal'],
                'rhpp_transfer_nominal' => $validated['rhpp_transfer_nominal'],
            ]);

            $period->update(['status' => 'closed']);

            // $dividends = Dividend::where('period_id', $period->id)
            //     ->with([
            //         'user' => function ($query) {
            //             $query->select('id', 'name', 'email');
            //         },
            //         'period',
            //     ])
            //     ->get();

            // foreach ($dividends as $dividend) {
            //     $dividend->user->notify(new ClosingPeriodInvestorNotification($dividend));
            // }

            // user admin dan teller
            // $users = User::whereHas('roles', function ($query) {
            //     $query->whereIN('name', ['admin', 'teller']);
            // })->get();

            // foreach ($users as $user) {
            //     $user->notify(new ClosingPeriodAdminNotification($closingPeriod));
            // }

            DB::commit();

            return (new SuccessResource('Berhasil menutup periode', $period))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function previewClosePeriod(PreviewClosingPeriodRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            $period = Period::findOrFail($id);

            if ($period->closingPeriod) {
                return (new ErrorResource(null, 'Periode tidak bisa ditutup karena sudah ada data penutupan periode'))
                    ->response()
                    ->setStatusCode(400);
            }

            $preview = collect($this->_getPreview($period, $validated, true));

            return (new SuccessResource('Preview tutup periode', $preview))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Periode tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    private function _getPreview(Period $period, $validated, $appendRevenue = false)
    {
        $debit = 0;
        $credit = 0;
        $balance = 0;

        $journals = $period->journals()
            ->orderBy('date', 'asc')
            ->get([
                'date', 'description', 'debit', 'credit',
            ]);

        if ($appendRevenue) {
            $journals[] = (object) [
                'date' => $period->end_date,
                'description' => 'Pendapatan Hasil Panen',
                'debit' => (float) $validated['revenue'],
                'credit' => 0,
            ];
        }

        foreach ($journals as $i => $journal) {
            $journal->no = $i + 1;
            $debit += $journal->debit;
            $credit += $journal->credit;
            $balance += $journal->debit - $journal->credit;
            $journal->balance = $balance;
        }

        $revenue = $debit;
        $expenses = $credit;
        $netProfit = $revenue - $expenses;
        $managementFee = (float) $validated['management_fee'];
        $cooperative = (float) $validated['cooperative_percentage'] / 100 * $netProfit;
        $zakat = (float) $validated['zakat_percentage'] / 100 * $netProfit;
        $dividend = $netProfit - $managementFee - $cooperative - $zakat;

        $dividendFoundations = $dividend * 0.5;
        $dividendInvestors = $dividend - $dividendFoundations;

        return (object) [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'management_fee' => $managementFee,
            'cooperative' => $cooperative,
            'zakat' => $zakat,
            'dividend' => $dividend,
            'dividend_foundations' => $dividendFoundations,
            'dividend_investors' => $dividendInvestors,
            'journals' => $journals,
        ];
    }
}
