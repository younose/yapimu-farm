<?php

namespace App\Http\Controllers\Dashboard\Dividend;

use App\DataTables\DividendReportDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Models\Dividend;
use App\Models\Period;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DividendReportController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Laporan Dividen');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Dividen', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(DividendReportDataTable $dataTable)
    {
        $openPeriod = Period::getOpenPeriod();
        $this->setData('openPeriod', $openPeriod);

        $allPeriods = Period::orderBy('start_date', 'desc')->get();
        foreach ($allPeriods as $i => $item) {
            $item->status_detail = $item->getStatuse($item->status);
        }
        $this->setData('allPeriods', $allPeriods);

        $currentPeriod = Period::find(request()->period_id) ?? $openPeriod;
        if ($currentPeriod) {
            $currentPeriod->load('closingPeriod');
            $currentPeriod->status_detail = $currentPeriod->getStatuse($currentPeriod->status);

        }
        // $this->setData('closingPeriod', $closingPeriod);
        $this->setData('currentPeriod', $currentPeriod);

        $dataTable->currentPeriod = $currentPeriod;

        return $dataTable
            ->render('dashboard.dividend.report.index', $this->data);
    }

    public function print($id)
    {
        if (auth()->user()->hasRole('investor')) {
            return $this->_printInvestor();
        }

        return $this->_printAdmin($id);
    }

    public function _printAdmin($id)
    {
        $period = Period::findOrfail($id);
        $closingPeriod = $period->closingPeriod;
        $dividends = Dividend::where('period_id', $id)->get();

        $dividends->load([
            'period',
            'user' => function ($query) {
                $query->select([
                    'id',
                    'name',
                    'shares',
                    'balance',
                ]);
            },
        ]);
        // order by user->shares desc
        $dividends = $dividends->sortByDesc(function ($dividend) {
            return $dividend->user->shares;
        });
        $dividends = $dividends->values();

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Laporan Bagi Hasil');

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
            ->setCellValue('A1', 'LAPORAN BAGI HASIL');

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

        $activeWorksheet
            ->mergeCells('A3:E3');
        $activeWorksheet
            ->getStyle('A3:E3')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:E2')
            ->getFont()
            ->setBold(true);
        $startPeriod = Carbon::parse($period->start_date)->format('d M Y');
        $endPeriod = Carbon::parse($period->end_date)->format('d M Y');
        $activeWorksheet
            ->setCellValue('A3', 'Periode: '.$period->name.' ('.$startPeriod.' - '.$endPeriod.')');

        $activeWorksheet
            ->setCellValue('A5', 'Jumlah Saham Dibuka');
        $activeWorksheet
            ->setCellValue('B5', getSetting('default_share_count'));

        $activeWorksheet
            ->setCellValue('A6', 'Jumlah Saham Diakuisisi');
        $activeWorksheet
            ->setCellValue('B6', User::sum('shares'));

        $activeWorksheet
            ->setCellValue('A7', 'Harga Perlembar Saham');
        $activeWorksheet
            ->setCellValue('B7', getSetting('default_share_price'));
        $activeWorksheet
            ->getStyle('B7')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('A9', 'Hasil Panen Periode');
        $activeWorksheet
            ->setCellValue('B9', $closingPeriod->dividend_investor);
        $activeWorksheet
            ->getStyle('B9')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('A10', 'Bagi Hasil Perlembar');
        $activeWorksheet
            ->setCellValue('B10', '=B9/B6');
        $activeWorksheet
            ->getStyle('B10')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $startRow = 12;
        // no | Nama | Jumlah Saham | JUmlah Dana | Bagi Hasil
        $activeWorksheet
            ->setCellValue('A'.$startRow, 'No');
        $activeWorksheet
            ->setCellValue('B'.$startRow, 'Nama');
        $activeWorksheet
            ->setCellValue('C'.$startRow, 'Jumlah Saham');
        $activeWorksheet
            ->setCellValue('D'.$startRow, 'Jumlah Dana');
        $activeWorksheet
            ->setCellValue('E'.$startRow, 'Bagi Hasil');

        $activeWorksheet
            ->getStyle('A'.$startRow.':E'.$startRow)
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->getStyle('A'.$startRow.':E'.$startRow)
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');

        foreach ($dividends as $i => $dividend) {
            $currentRow = $startRow + (int) $i + 1;
            $activeWorksheet
                ->setCellValue('A'.($currentRow), (int) $i + 1);
            $activeWorksheet
                ->setCellValue('B'.($currentRow), $dividend->user->name);
            $activeWorksheet
                ->setCellValue('C'.($currentRow), $dividend->user->shares);
            $activeWorksheet
                ->setCellValue('D'.($currentRow), '=C'.($currentRow).'*B7');
            $activeWorksheet
                ->setCellValue('E'.($currentRow), '=C'.($currentRow).'*B10');

            $activeWorksheet
                ->getStyle('D'.($currentRow))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $activeWorksheet
                ->getStyle('E'.($currentRow))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        $activeWorksheet
            ->getStyle('A'.$startRow.':E'.$currentRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        foreach (range('A', 'E') as $column) {
            $activeWorksheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Laporan Bagi Hasil Periode %s (%s - %s) - %s.xlsx';
        $name = sprintf($name, $period->name, $startPeriod, $endPeriod, config('app.name'));
        $writer->save($name);

        // download
        return response()
            ->download($name)
            ->deleteFileAfterSend(true);
    }

    public function _printInvestor()
    {
        $dividends = Dividend::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->with([
                'period',
                'user' => function ($query) {
                    $query->select([
                        'id',
                        'name',
                        'shares',
                        'balance',
                    ]);
                },
            ])
            ->get();

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Laporan Bagi Hasil');

        $activeWorksheet
            ->mergeCells('A1:G1');
        $activeWorksheet
            ->getStyle('A1:G1')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A1:G1')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A1', 'LAPORAN BAGI HASIL');

        $activeWorksheet
            ->mergeCells('A2:G2');
        $activeWorksheet
            ->getStyle('A2:G2')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:G2')
            ->getFont()
            ->setBold(true);
        $activeWorksheet
            ->setCellValue('A2', config('app.name'));

        $activeWorksheet
            ->mergeCells('A3:G3');
        $activeWorksheet
            ->getStyle('A3:G3')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A2:G2')
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->setCellValue('A3', 'Investor: '.auth()->user()->name);

        $startRow = 5;
        // no | tanggal | periode | jumlah saham | hasil periode | perlembar | bagi hasil
        $activeWorksheet
            ->setCellValue('A'.$startRow, 'No');
        $activeWorksheet
            ->setCellValue('B'.$startRow, 'Tanggal');
        $activeWorksheet
            ->setCellValue('C'.$startRow, 'Periode');
        $activeWorksheet
            ->setCellValue('D'.$startRow, 'Jumlah Saham');
        $activeWorksheet
            ->setCellValue('E'.$startRow, 'Hasil Periode');
        $activeWorksheet
            ->setCellValue('F'.$startRow, 'Perlembar');
        $activeWorksheet
            ->setCellValue('G'.$startRow, 'Bagi Hasil');

        foreach ($dividends as $i => $dividend) {
            $currentRow = $startRow + (int) $i + 1;
            $activeWorksheet
                ->setCellValue('A'.($currentRow), (int) $i + 1);
            $activeWorksheet
                ->setCellValue('B'.($currentRow), $dividend->created_at->format('d M Y'));
            $activeWorksheet
                ->setCellValue('C'.($currentRow), $dividend->period->name);
            $activeWorksheet
                ->setCellValue('D'.($currentRow), $dividend->shares);
            $activeWorksheet
                ->setCellValue('E'.($currentRow), $dividend->period_result);
            $activeWorksheet
                ->setCellValue('F'.($currentRow), $dividend->dividend);
            $activeWorksheet
                ->setCellValue('G'.($currentRow), '=F'.($currentRow).'*D'.($currentRow));

            $activeWorksheet
                ->getStyle('E'.($currentRow).':G'.($currentRow))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        $activeWorksheet
            ->getStyle('A'.$startRow.':G'.$currentRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        foreach (range('A', 'G') as $column) {
            $activeWorksheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Laporan Bagi Hasil Investor %s - %s.xlsx';
        $name = sprintf($name, auth()->user()->name, date('d M Y His'));
        $writer->save($name);

        // download
        return response()
            ->download($name)
            ->deleteFileAfterSend(true);
    }
}
