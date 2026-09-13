<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\InvestorDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Investor\InvestorCreateRequest;
use App\Http\Requests\Investor\InvestorUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Activitylog\Models\Activity;

class InvestorController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Investor');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Investor', '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(InvestorDataTable $dataTable)
    {
        return $dataTable
            ->render('dashboard.investor.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $investor = User::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data investor', $investor))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Investor tidak ditemukan'))
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
    public function store(InvestorCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $investor = User::create($validated);
            $investor->markEmailAsVerified();

            $investor->assignRole('investor');

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambah investor baru', $investor))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambah investor baru');
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
    public function update(InvestorUpdateRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $investor = User::findOrFail($id);
            $investor->update($validated);

            return (new SuccessResource('Berhasil mengubah investor', $investor))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Investor tidak ditemukan'))
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
            $investor = User::findOrFail($id);

            if ($investor->shares > 0) {
                $th = new \Exception('Investor masih memiliki '.$investor->shares.' lembar saham');

                return (new ErrorResource($th, 'Investor tidak bisa dihapus karena masih memiliki saham'))
                    ->response()
                    ->setStatusCode(400);
            }

            $investor->removeRole('investor');
            $activity = Activity::where('subject_id', $id)
                ->where('subject_type', User::class)
                ->get();
            $activity->each->delete();
            $investor->delete();

            return (new SuccessResource('Berhasil menghapus investor', $investor))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Investor tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Investor tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Laporan Periode');

        $activeWorksheet
            ->mergeCells('A1:H1');
        $activeWorksheet
            ->setCellValue('A1', 'DATA INVESTOR');

        $activeWorksheet
            ->mergeCells('A2:H2');
        $activeWorksheet
            ->setCellValue('A2', config('app.name'));

        $activeWorksheet
            ->mergeCells('A3:H3');
        $activeWorksheet
            ->setCellValue('A3', 'Tanggal: '.now()->format('d F Y H:i:s'));
        $activeWorksheet
            ->getStyle('A1:H3')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A1:H2')
            ->getFont()
            ->setBold(true);

        // A5 Saham dibuka
        $shares = getSetting('default_share_count');
        $activeWorksheet
            ->setCellValue('A5', 'Saham dibuka:');
        $activeWorksheet
            ->setCellValue('B5', $shares);

        // A6 Saham diakuisisi
        $shares = User::investors()->sum('shares');
        $activeWorksheet
            ->setCellValue('A6', 'Saham diakuisisi:');
        $activeWorksheet
            ->setCellValue('B6', $shares);

        // A7 harga per saham
        $price = getSetting('default_share_price');
        $activeWorksheet
            ->setCellValue('A7', 'Harga per saham:');
        $activeWorksheet
            ->setCellValue('B7', $price);

        // A8 total modal
        $total = $shares * $price;
        $activeWorksheet
            ->setCellValue('A8', 'Total modal:');
        $activeWorksheet
            ->setCellValue('B8', $total);

        // A9 Saldo Investor
        $activeWorksheet
            ->setCellValue('A9', 'Saldo Investor:');

        $activeWorksheet
            ->getStyle('A5:A9')
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->getStyle('B7:B9')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $investores = User::investors()
            ->orderBy('name')
            ->get();

        $startRow = 11;
        $activeWorksheet
            ->setCellValue('A'.$startRow, 'No');
        $activeWorksheet
            ->setCellValue('B'.$startRow, 'Nama');
        $activeWorksheet
            ->setCellValue('C'.$startRow, 'Email');
        $activeWorksheet
            ->setCellValue('D'.$startRow, 'Saham');
        $activeWorksheet
            ->setCellValue('E'.$startRow, 'Dana Investor');
        $activeWorksheet
            ->setCellValue('F'.$startRow, 'Saldo');
        $activeWorksheet
            ->setCellValue('G'.$startRow, 'Debit');
        $activeWorksheet
            ->setCellValue('H'.$startRow, 'Kredit');

        $activeWorksheet
            ->getStyle('A'.$startRow.':H'.$startRow)
            ->getFont()
            ->setBold(true);

        $activeWorksheet
            ->getStyle('A'.$startRow.':H'.$startRow)
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');

        foreach ($investores as $i => $investor) {
            $startRow++;
            $activeWorksheet
                ->setCellValue('A'.$startRow, $i + 1);
            $activeWorksheet
                ->setCellValue('B'.$startRow, $investor->name);
            $activeWorksheet
                ->setCellValue('C'.$startRow, $investor->email);
            $activeWorksheet
                ->setCellValue('D'.$startRow, $investor->shares);
            $activeWorksheet
                ->setCellValue('E'.$startRow, "=D$startRow * B7");
            $debit = $investor
                ->transactions()
                ->where('type', 'debit')
                ->sum('amount');
            $credit = $investor
                ->transactions()
                ->where('type', 'credit')
                ->sum('amount');
            $balance = $debit - $credit;
            $activeWorksheet
                ->setCellValue('F'.$startRow, $balance);
            $activeWorksheet
                ->setCellValue('G'.$startRow, $debit);
            $activeWorksheet
                ->setCellValue('H'.$startRow, $credit);
        }

        // B9 sum F
        $activeWorksheet
            ->setCellValue('B9', "=SUM(F12:F$startRow)");

        $activeWorksheet
            ->getStyle('E'.($startRow - count($investores)).':H'.$startRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // border
        $activeWorksheet
            ->getStyle('A'.$startRow - count($investores).':H'.$startRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        // auto column width
        foreach (range('A', 'H') as $column) {
            $spreadsheet
                ->getActiveSheet()
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Data Investor '.now()->format('d F Y His').'.xlsx';
        $writer->save($name);

        return response()
            ->download($name)
            ->deleteFileAfterSend(true);
    }
}
