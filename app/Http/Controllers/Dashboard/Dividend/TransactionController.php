<?php

namespace App\Http\Controllers\Dashboard\Dividend;

use App\DataTables\TransactionDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Period;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Laporan Saldo Investor');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Dividen', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(TransactionDataTable $dataTable)
    {
        $investors = User::investors()
            ->orderBy('name')
            ->select('id', 'name', 'photo')
            ->get();
        $this->setData('investors', $investors);

        return $dataTable
            ->render('dashboard.dividend.transaction.index', $this->data);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:1',
                'description' => 'required|string',
                'type' => 'required|in:debit,credit',
                'date' => 'required|date_format:Y-m-d',
            ]);

            $user = User::find($validated['user_id']);
            $period = Period::getOpenPeriod();
            if (!$period) {
                throw new \Exception('Tidak ada periode aktif!');
            }

            $amount = $request->has('is_minus') ? -$validated['amount'] : $validated['amount'];

            $transaction = $user->transactions()
                ->create([
                    'reference_id' => $period->id,
                    'description' => $validated['description'],
                    'type' => $validated['type'],
                    'amount' => $amount,
                    'created_at' => $validated['date'] . ' 00:00:00',
                ]);

            $user->balance += floatval($amount);


            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambahkan transaksi baru', $transaction))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambahkan transaksi baru');
        } catch (\Throwable $th) {
            if ($request->ajax() || $request->wantsJson()) {
                return (new ErrorResource($th, 'Terjadi kesalahan!'))
                    ->response()
                    ->setStatusCode(500);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    public function print($id)
    {
        $user = User::findOrfail($id);
        $transactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->get();

        if (request('format') === 'pdf') {
            return $this->_exportPdf($user, $transactions);
        }

        return $this->_exportExcel($user, $transactions);
    }

    /**
     * Company letterhead ("kop") data shared by the PDF and Excel exports.
     */
    private function _kop()
    {
        $logoValue = getSetting('app_logo_full') ?? getSetting('app_logo');
        $logoPath = $logoValue && Storage::disk('public')->exists($logoValue)
            ? Storage::disk('public')->path($logoValue)
            : null;

        return (object) [
            'name' => getSetting('app_name', config('app.name')),
            'description' => getSetting('app_description'),
            'logo_path' => $logoPath,
        ];
    }

    private function _exportPdf($user, $transactions)
    {
        $kop = $this->_kop();
        $logoBase64 = null;
        if ($kop->logo_path) {
            $mime = mime_content_type($kop->logo_path) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($kop->logo_path));
        }

        $debit = $user->transactions()->where('type', 'debit')->sum('amount');
        $credit = $user->transactions()->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        $pdf = Pdf::loadView('dashboard.dividend.transaction.print-pdf', [
            'kop' => $kop,
            'logoBase64' => $logoBase64,
            'user' => $user,
            'transactions' => $transactions,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
        ])->setPaper('a4', 'portrait');

        $name = sprintf('Laporan Saldo Investor %s.pdf', $user->name);

        return $pdf->download($name);
    }

    private function _exportExcel($user, $transactions)
    {
        $kop = $this->_kop();

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet
            ->getActiveSheet();
        $activeWorksheet
            ->setTitle('Laporan Saldo Investor');

        // kop (letterhead): logo + institution name + description
        $activeWorksheet
            ->getRowDimension(1)
            ->setRowHeight(22);
        $activeWorksheet
            ->getRowDimension(2)
            ->setRowHeight(16);
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
            ->setBold(true)
            ->setSize(15);
        $activeWorksheet
            ->setCellValue('A1', strtoupper($kop->name));

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
            ->setItalic(true)
            ->setSize(9);
        $activeWorksheet
            ->setCellValue('A2', $kop->description);

        $activeWorksheet
            ->getStyle('A3:E3')
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        if ($kop->logo_path) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setPath($kop->logo_path);
            $drawing->setHeight(45);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(4);
            $drawing->setOffsetY(4);
            $drawing->setWorksheet($activeWorksheet);
        }

        $activeWorksheet
            ->mergeCells('A5:E5');
        $activeWorksheet
            ->getStyle('A5:E5')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');
        $activeWorksheet
            ->getStyle('A5:E5')
            ->getFont()
            ->setBold(true)
            ->setSize(12)
            ->setUnderline(true);
        $activeWorksheet
            ->setCellValue('A5', 'LAPORAN SALDO INVESTOR');

        $activeWorksheet
            ->setCellValue('A7', 'Nama');
        $activeWorksheet
            ->setCellValue('B7', $user->name);

        $activeWorksheet
            ->setCellValue('A8', 'Lembar Saham');
        $activeWorksheet
            ->setCellValue('B8', $user->shares);

        $activeWorksheet
            ->setCellValue('A9', 'Harga Perlembar');
        $activeWorksheet
            ->setCellValue('B9', getSetting('default_share_price'));
        $activeWorksheet
            ->getStyle('B9')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('A10', 'Jumlah Dana');
        $activeWorksheet
            ->setCellValue('B10', '=B8*B9');
        $activeWorksheet
            ->getStyle('B10')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $debit = $user->transactions()
            ->where('type', 'debit')
            ->sum('amount');
        $credit = $user->transactions()
            ->where('type', 'credit')
            ->sum('amount');
        $balance = $debit - $credit;

        $activeWorksheet
            ->setCellValue('A12', 'Masuk');
        $activeWorksheet
            ->setCellValue('B12', $debit);
        $activeWorksheet
            ->getStyle('B12')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('A13', 'Keluar');
        $activeWorksheet
            ->setCellValue('B13', $credit);
        $activeWorksheet
            ->getStyle('B13')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $activeWorksheet
            ->setCellValue('A14', 'Saldo');
        $activeWorksheet
            ->setCellValue('B14', $balance);
        $activeWorksheet
            ->getStyle('B14')
            ->getNumberFormat()
            ->setFormatCode('#,##0');
        $activeWorksheet
            ->getStyle('A14:B14')
            ->getFont()
            ->setBold(true);

        $startRow = 16;
        $activeWorksheet
            ->setCellValue('A' . $startRow, 'No');
        $activeWorksheet
            ->setCellValue('B' . $startRow, 'Tanggal');
        $activeWorksheet
            ->setCellValue('C' . $startRow, 'Keterangan');
        $activeWorksheet
            ->setCellValue('D' . $startRow, 'Jenis');
        $activeWorksheet
            ->setCellValue('E' . $startRow, 'Jumlah');

        $startRow++;
        foreach ($transactions as $i => $transaction) {
            $activeWorksheet
                ->setCellValue('A' . $startRow, $i + 1);
            $activeWorksheet
                ->setCellValue('B' . $startRow, $transaction->created_at->format('d/m/Y'));
            $activeWorksheet
                ->setCellValue('C' . $startRow, $transaction->description);
            $type = $transaction->type == 'debit' ? 'Masuk' : 'Keluar';
            $activeWorksheet
                ->setCellValue('D' . $startRow, $type);
            $activeWorksheet
                ->setCellValue('E' . $startRow, $transaction->amount);
            $activeWorksheet
                ->getStyle('E' . $startRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            $startRow++;
        }

        $activeWorksheet
            ->getStyle('A16:E16')
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB('FFFFFFFF');
        $activeWorksheet
            ->getStyle('A16:E16')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF1D2939');
        $activeWorksheet
            ->getStyle('A16:E16')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('middle');

        $activeWorksheet
            ->getStyle('A16:E' . ($startRow - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        foreach (range('A', 'E') as $column) {
            $activeWorksheet
                ->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $name = 'Laporan Saldo Investor %s.xlsx';
        $name = sprintf($name, $user->name);
        $writer->save($name);

        return response()
            ->download($name)
            ->deleteFileAfterSend(true);
    }
}
