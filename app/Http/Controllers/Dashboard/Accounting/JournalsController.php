<?php

namespace App\Http\Controllers\Dashboard\Accounting;

use App\DataTables\JournalDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\Journal\CreateJournalrequest;
use App\Http\Requests\Dashbaord\Journal\UpdateJournalrequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Journal;
use App\Models\Period;

class JournalsController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Catatan Keuangan');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Laporan Keuangan', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(JournalDataTable $dataTable)
    {
        $openPeriod = Period::getOpenPeriod();
        $this->setData('openPeriod', $openPeriod);

        $allPeriods = Period::orderBy('start_date', 'desc')->get();

        foreach ($allPeriods as $i => $item) {
            $item->status_detail = $item->getStatuse($item->status);
        }

        $this->setData('allPeriods', $allPeriods);

        $currentPeriod = Period::find(request()->period_id) ?? $openPeriod;
        $this->setData('currentPeriod', $currentPeriod);

        $periods = Period::where('status', 'open')
            ->orWhereDoesntHave('closingPeriod')
            ->orderBy('start_date', 'desc')
            ->get();
        $this->setData('periods', $periods);

        $debit = 0;
        $credit = 0;
        $saldo = 0;

        if ($currentPeriod) {
            $debit = Journal::where('period_id', $currentPeriod->id)->sum('debit');
            $credit = Journal::where('period_id', $currentPeriod->id)->sum('credit');
            $saldo = $debit - $credit;

            $currentPeriod->status_detail = $currentPeriod->getStatuse($currentPeriod->status);
        }

        $this->setData('debit', idrFormat($debit));
        $this->setData('credit', idrFormat($credit));
        $this->setData('saldo', idrFormat($saldo));

        $dataTable->debit = $debit;
        $dataTable->credit = $credit;
        $dataTable->saldo = $saldo;
        $dataTable->currentPeriod = $currentPeriod;

        return $dataTable
            ->render('dashboard.accounting.journal.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $journal = Journal::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data Laporan Keuangan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateJournalrequest $request)
    {
        try {
            $validated = $request->validated();
            $period = Period::findOrFail($validated['period_id']);
            if ($period->closingPeriod) {
                return (new ErrorResource(null, 'Periode sudah ditutup, tidak bisa menambahkan laporan keuangan'))
                    ->response()
                    ->setStatusCode(400);
            }

            $journal = Journal::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambahkan laporan keuangan baru', $journal))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambahkan laporan keuangan baru');
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
    public function update(UpdateJournalrequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            $journal = Journal::findOrFail($id);
            $period = $journal->period;

            if ($period->closingPeriod) {
                return (new ErrorResource(null, 'Periode sudah ditutup, tidak bisa mengubah laporan keuangan'))
                    ->response()
                    ->setStatusCode(400);
            }

            $journal->update($validated);

            return (new SuccessResource('Berhasil mengubah Laporan Keuangan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan tidak ditemukan'))
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
            $journal = Journal::findOrFail($id);
            $period = $journal->period;

            if ($period->closingPeriod) {
                return (new ErrorResource(null, 'Periode sudah ditutup, tidak bisa mengubah laporan keuangan'))
                    ->response()
                    ->setStatusCode(400);
            }

            $journal->delete();

            return (new SuccessResource('Berhasil menghapus Laporan Keuangan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
