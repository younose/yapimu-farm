<?php

namespace App\Http\Controllers\Dashboard\Accounting;

use App\DataTables\JaminanJournalDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\JaminanJournal\CreateJaminanJournalRequest;
use App\Http\Requests\Dashbaord\JaminanJournal\UpdateJaminanJournalRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\JaminanJournal;

class JaminanJournalsController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Jaminan');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Laporan Keuangan', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(JaminanJournalDataTable $dataTable)
    {
        $debit = JaminanJournal::sumDebit();
        $credit = JaminanJournal::sumCredit();
        $saldo = $debit - $credit;

        $this->setData('debit', idrFormat($debit));
        $this->setData('credit', idrFormat($credit));
        $this->setData('saldo', idrFormat($saldo));

        $dataTable->debit = $debit;
        $dataTable->credit = $credit;
        $dataTable->saldo = $saldo;

        return $dataTable
            ->render('dashboard.jaminan-journal.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $journal = JaminanJournal::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data Jaminan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Data Jaminan tidak ditemukan'))
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
    public function store(CreateJaminanJournalRequest $request)
    {
        try {
            $journal = JaminanJournal::create($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambahkan data Jaminan', $journal))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambahkan data Jaminan');
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
    public function update(UpdateJaminanJournalRequest $request, string $id)
    {
        try {
            $journal = JaminanJournal::findOrFail($id);

            if ($journal->isAutomatic()) {
                return (new ErrorResource(null, 'Data ini tercatat otomatis dari selisih RHPP periode dan tidak bisa diubah manual'))
                    ->response()
                    ->setStatusCode(400);
            }

            $journal->update($request->validated());

            return (new SuccessResource('Berhasil mengubah data Jaminan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Data Jaminan tidak ditemukan'))
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
            $journal = JaminanJournal::findOrFail($id);

            if ($journal->isAutomatic()) {
                return (new ErrorResource(null, 'Data ini tercatat otomatis dari selisih RHPP periode dan tidak bisa dihapus manual'))
                    ->response()
                    ->setStatusCode(400);
            }

            $journal->delete();

            return (new SuccessResource('Berhasil menghapus data Jaminan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Data Jaminan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
