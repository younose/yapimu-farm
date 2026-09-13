<?php

namespace App\Http\Controllers\Dashboard\Accounting;

use App\DataTables\PeriodicReportDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Dashbaord\Period\UpdateClosingDocumentsRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\ClosingPeriod;
use App\Models\Period;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PeriodicReportController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Laporan Periode');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Laporan Keuangan', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(PeriodicReportDataTable $dataTable)
    {
        $latestClosing = ClosingPeriod::with('period')
            ->orderByDesc('period_id')
            ->first();
        $this->setData('latestClosing', $latestClosing);
        $this->setData('totalClosed', ClosingPeriod::count());

        return $dataTable
            ->render('dashboard.accounting.periodic-report.index', $this->data);
    }

    public function show($id)
    {
        try {
            $closing = ClosingPeriod::findOrFail($id);
            $period = $closing->period;
            $validated = [
                'revenue' => $closing->revenue,
                'management_fee' => $period->management_fee,
                'cooperative_percentage' => $period->cooperative_percentage,
                'zakat_percentage' => $period->zakat_percentage,
            ];

            $preview = collect($this->_getPreview($period, $validated));
            $preview['rhpp_document_url'] = $closing->rhpp_document_url;
            $preview['rhpp_nominal'] = $closing->rhpp_nominal;
            $preview['rhpp_transfer_proof_url'] = $closing->rhpp_transfer_proof_url;
            $preview['rhpp_transfer_nominal'] = $closing->rhpp_transfer_nominal;
            $preview['rhpp_selisih'] = (!is_null($closing->rhpp_nominal) && !is_null($closing->rhpp_transfer_nominal))
                ? $closing->rhpp_nominal - $closing->rhpp_transfer_nominal
                : null;

            return (new SuccessResource('Detail laporan', $preview))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function print($id)
    {
        try {
            $closing = ClosingPeriod::findOrFail($id);

            return $this->_export($closing);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            abort(404);
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    /**
     * Attach or replace the RHPP document / transfer proof on a period that
     * was already closed before this feature existed (or to correct a
     * wrong upload).
     */
    public function updateDocuments(UpdateClosingDocumentsRequest $request, string $id)
    {
        try {
            $closing = ClosingPeriod::findOrFail($id);
            $validated = $request->validated();

            $hasAnyInput = $request->hasFile('rhpp_document')
                || $request->hasFile('rhpp_transfer_proof')
                || $request->filled('rhpp_nominal')
                || $request->filled('rhpp_transfer_nominal');

            if (!$hasAnyInput) {
                return (new ErrorResource(null, 'Isi minimal salah satu dokumen atau nominal untuk disimpan'))
                    ->response()
                    ->setStatusCode(422);
            }

            if ($request->hasFile('rhpp_document')) {
                $closing->rhpp_document = $closing->updateFile(ClosingPeriod::UPLOAD_PATH, $closing->rhpp_document, $request->file('rhpp_document'));
            }

            if ($request->hasFile('rhpp_transfer_proof')) {
                $closing->rhpp_transfer_proof = $closing->updateFile(ClosingPeriod::UPLOAD_PATH, $closing->rhpp_transfer_proof, $request->file('rhpp_transfer_proof'));
            }

            if ($request->filled('rhpp_nominal')) {
                $closing->rhpp_nominal = $validated['rhpp_nominal'];
            }

            if ($request->filled('rhpp_transfer_nominal')) {
                $closing->rhpp_transfer_nominal = $validated['rhpp_transfer_nominal'];
            }

            $closing->save();

            return (new SuccessResource('Berhasil memperbarui dokumen RHPP', [
                'rhpp_document_url' => $closing->rhpp_document_url,
                'rhpp_nominal' => $closing->rhpp_nominal,
                'rhpp_transfer_proof_url' => $closing->rhpp_transfer_proof_url,
                'rhpp_transfer_nominal' => $closing->rhpp_transfer_nominal,
            ]))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Laporan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function printProof($id)
    {
        try {
            $this->setTitle('Bukti Nota');

            $closing = ClosingPeriod::findOrFail($id);
            $journals = $closing->period->journals()
                ->hasProof()
                ->orderBy('date', 'asc')
                ->get();
            $this->setData('journals', $journals);
            $this->setData('closing', $closing);

            return view('dashboard.accounting.periodic-report.print-proof', $this->data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            abort(404);
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    private function _getPreview(Period $period, $validated)
    {
        $debit = 0;
        $credit = 0;
        $balance = 0;

        $journals = $period->journals()
            ->orderBy('date', 'asc')
            ->get([
                'date', 'description', 'debit', 'credit',
            ]);

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

    private function _export($closingPeriod)
    {
        $period = $closingPeriod->period;
        $validated = [
            'revenue' => $closingPeriod->revenue,
            'management_fee' => $period->management_fee,
            'cooperative_percentage' => $period->cooperative_percentage,
            'zakat_percentage' => $period->zakat_percentage,
        ];

        $preview = collect($this->_getPreview($period, $validated));

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Laporan Periode');

        $activeWorksheet
            ->mergeCells('A1:F1');
        $activeWorksheet
            ->getStyle('A1:F1')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A1:F1')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A1', 'LAPORAN TUTUP PERIODE');

        $activeWorksheet
            ->mergeCells('A2:F2');
        $activeWorksheet
            ->getStyle('A2:F2')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:F2')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A2', config('app.name'));

        $activeWorksheet
            ->mergeCells('A3:F3');
        $activeWorksheet
            ->getStyle('A3:F3')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:F2')
            ->getFont()
            ->setBold(true);
        $startPeriod = Carbon::parse($period->start_date)->format('d M Y');
        $endPeriod = Carbon::parse($period->end_date)->format('d M Y');
        $activeWorksheet
            ->setCellValue('A3', 'Periode: ' . $period->name . ' (' . $startPeriod . ' - ' . $endPeriod . ')');

        $activeWorksheet
            ->setCellValue('A5', 'No');
        $activeWorksheet
            ->setCellValue('B5', 'Tanggal');
        $activeWorksheet
            ->setCellValue('C5', 'Uraian');
        $activeWorksheet
            ->setCellValue('D5', 'Masuk');
        $activeWorksheet
            ->setCellValue('E5', 'Keluar');
        $activeWorksheet
            ->setCellValue('F5', 'Saldo');

        $activeWorksheet
            ->getStyle('A5:F5')
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->getStyle('A5:F5')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');

        $startRow = 6;
        $journals = $preview['journals'];
        foreach ($journals as $journal) {
            $activeWorksheet
                ->setCellValue('A' . $startRow, $journal->no);
            $activeWorksheet
                ->setCellValue('B' . $startRow, $journal->date);
            $activeWorksheet
                ->setCellValue('C' . $startRow, $journal->description);
            $activeWorksheet
                ->setCellValue('D' . $startRow, $journal->debit);
            $activeWorksheet
                ->setCellValue('E' . $startRow, $journal->credit);
            $activeWorksheet
                ->setCellValue('F' . $startRow, $journal->balance);

            $startRow++;
        }

        // sety date format
        $activeWorksheet
            ->getStyle('B6:B' . $startRow - 1)
            ->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy');

        // $activeWorksheet
        //     ->getStyle('D6:F' . $startRow - 1)
        //     ->getNumberFormat()
        //     ->setFormatCode('_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)');

        $activeWorksheet
            ->getStyle('D6:F' . $startRow - 1)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->getStyle('A5:F' . $startRow - 1)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'SISA HASIL PANEN');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['net_profit']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'MANAJEMEN');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['management_fee']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'KOPERASI');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['cooperative']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'ZAKAT MAL');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['zakat']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');
        $activeWorksheet
            ->getStyle('C' . $startRow . ':D' . $startRow)
            ->getBorders()
            ->getBottom()
            ->setBorderStyle('thin');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'SISA HASIL PANEN');
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['dividend']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');
        $activeWorksheet
            ->getStyle('C' . $startRow . ':D' . $startRow)
            ->getFont()
            ->setBold(true);

        $startRow++;
        $startRow++;
        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'DIBAGI DUA KANDANG (YAYASAN dan INVESTASI)');
        $activeWorksheet
            ->getStyle('C' . $startRow . ':D' . $startRow)
            ->getFont()
            ->setBold(true);

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'Kandang Yayasan');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['dividend_foundations']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $startRow++;
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'Kandang Investasi');
        $activeWorksheet
            ->setCellValue('D' . $startRow, $preview['dividend_investors']);
        $activeWorksheet
            ->getStyle('D' . $startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        foreach (range('A', 'F') as $col) {
            $activeWorksheet
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Laporan Tutup Periode %s (%s - %s) - %s.xlsx';
        $name = sprintf($name, $period->name, $startPeriod, $endPeriod, config('app.name'));
        $writer->save($name);

        // download
        return response()
            ->download($name)
            ->deleteFileAfterSend(true);
    }
}
