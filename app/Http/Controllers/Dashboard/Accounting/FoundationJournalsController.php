<?php

namespace App\Http\Controllers\Dashboard\Accounting;

use App\DataTables\FoundationJournalDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\FoundationJournal\CreateFoundationJournalrequest;
use App\Http\Requests\Dashbaord\FoundationJournal\UpdateFoundationJournalrequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\FoundationJournal;
use App\Models\Period;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FoundationJournalsController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Catatan Keuangan Yayasan');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Laporan Keuangan', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FoundationJournalDataTable $dataTable)
    {
        $debit = FoundationJournal::sum('debit');
        $credit = FoundationJournal::sum('credit');
        $saldo = $debit - $credit;

        $this->setData('debit', idrFormat($debit));
        $this->setData('credit', idrFormat($credit));
        $this->setData('saldo', idrFormat($saldo));

        $dataTable->debit = $debit;
        $dataTable->credit = $credit;
        $dataTable->saldo = $saldo;

        return $dataTable
            ->render('dashboard.accounting.foundation-journal.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $journal = FoundationJournal::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data Laporan Keuangan Yasayan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan Yayasan tidak ditemukan'))
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
    public function store(CreateFoundationJournalrequest $request)
    {
        try {
            $validated = $request->validated();
            $openPeriod = Period::getOpenPeriod();
            if ($openPeriod) {
                $validated['period_id'] = $openPeriod->id;
            }
            $journal = FoundationJournal::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambahkan laporan keuangan yayasan', $journal))
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
    public function update(UpdateFoundationJournalrequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            $journal = FoundationJournal::findOrFail($id);
            $journal->update($validated);

            return (new SuccessResource('Berhasil mengubah Laporan Keuangan Yayasan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan Yayasan tidak ditemukan'))
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
            $journal = FoundationJournal::findOrFail($id);
            $journal->delete();

            return (new SuccessResource('Berhasil menghapus Laporan Keuangan Yayasan', $journal))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan Yayasan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Laporan Keuangan Yayasan tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function print()
    {
        $rangeTanggal = 'Semua Transaksi';
        if (request('start_date') && request('end_date')) {
            $rangeTanggal = request('start_date').' - '.request('end_date');
        }

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Catatan Keuangan Yayasan');

        $activeWorksheet
            ->mergeCells('A1:E1');
        $activeWorksheet
            ->getStyle('A1:E1')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A1:E1')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A1', 'CATATAN KEUANGAN YAYASAN');

        $activeWorksheet
            ->mergeCells('A2:E2');
        $activeWorksheet
            ->getStyle('A2:E2')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:E2')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A2', config('app.name'));

        $totalDebit = FoundationJournal::sum('debit');
        $totalCredit = FoundationJournal::sum('credit');
        $saldo = $totalDebit - $totalCredit;

        $activeWorksheet
            ->setCellValue('A4', 'Saldo Total');

        $activeWorksheet
            ->setCellValue('A5', 'Debit Total');
        $activeWorksheet
            ->setCellValue('B5', $totalDebit);

        $activeWorksheet
            ->setCellValue('A6', 'Kredit Total');
        $activeWorksheet
            ->setCellValue('B6', $totalCredit);

        $activeWorksheet
            ->setCellValue('B4', '=B5-B6');

        $activeWorksheet
            ->setCellValue('A8', 'Rentang Tanggal');
        $activeWorksheet
            ->setCellValue('B8', $rangeTanggal);

        $activeWorksheet
            ->setCellValue('A9', 'Saldo');
        $activeWorksheet
            ->setCellValue('A10', 'Debit');
        $activeWorksheet
            ->setCellValue('A11', 'Kredit');

        $activeWorksheet
            ->getStyle('A4:A11')
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->getStyle('B4:B6')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->getStyle('B9:B11')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $transactions = FoundationJournal::when(request('start_date') && request('end_date'), function ($query) {
            $query->whereBetween('date', [request('start_date'), request('end_date')]);
        })
            ->orderBy('date', 'asc')
            ->get();

        $startRow = 13;
        $activeWorksheet
            ->setCellValue('A'.$startRow, 'Tanggal');
        $activeWorksheet
            ->setCellValue('B'.$startRow, 'Deskripsi');
        $activeWorksheet
            ->setCellValue('C'.$startRow, 'Debit');
        $activeWorksheet
            ->setCellValue('D'.$startRow, 'Kredit');
        $activeWorksheet
            ->setCellValue('E'.$startRow, 'Bukti Nota');

        $activeWorksheet
            ->getStyle('A'.$startRow.':E'.$startRow)
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->getStyle('A'.$startRow.':E'.$startRow)
            ->getAlignment()
            ->setVertical('middle')
            ->setHorizontal('center');

        $startRow++;
        foreach ($transactions as $transaction) {
            $activeWorksheet
                ->setCellValue('A'.$startRow, $transaction->date);
            $activeWorksheet
                ->setCellValue('B'.$startRow, $transaction->description);
            $activeWorksheet
                ->setCellValue('C'.$startRow, $transaction->debit);
            $activeWorksheet
                ->setCellValue('D'.$startRow, $transaction->credit);

            if ($transaction->proof_url) {
                $activeWorksheet
                    ->getCell('E'.$startRow)
                    ->getHyperlink()
                    ->setUrl($transaction->proof_url);
                $activeWorksheet
                    ->getCell('E'.$startRow)
                    ->setValue('Nota');
            }

            $startRow++;
        }

        // format the number
        $activeWorksheet
            ->getStyle('C'.($startRow - count($transactions)).':D'.($startRow - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('B10', '=SUM(C'.($startRow - count($transactions)).':C'.($startRow - 1).')');
        $activeWorksheet
            ->setCellValue('B11', '=SUM(D'.($startRow - count($transactions)).':D'.($startRow - 1).')');
        $activeWorksheet
            ->setCellValue('B9', '=B10-B11');

        $activeWorksheet
            ->getStyle('A'.($startRow - count($transactions)) - 1 .':E'.($startRow - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        foreach (range('A', 'E') as $column) {
            $activeWorksheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Catatan Keuangan Yayasan - %s - %s.xlsx';
        $range = 'Semua Transaksi';

        if (request('start_date') && request('end_date')) {
            $range = request('start_date').' - '.request('end_date');
        }

        $name = sprintf($name, $range, config('app.name'));

        $writer->save($name);

        return response()
            ->download($name)
            ->deleteFileAfterSend(true);

    }
}
