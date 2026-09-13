<?php

namespace App\Http\Controllers\Dashboard\Accounting;

use App\DataTables\PaymentDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Payment;

class PaymentController extends DashboardController
{
    public function __construct()
    {
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Laporan Keuangan', '#');
    }

    public function index(PaymentDataTable $dataTable, string $type = 'zakat')
    {
        $title = 'Pembayaran '.ucfirst($type);
        $this->setTitle($title);
        $this->addBreadcrumb($this->getTitle(), '#');

        $unpaidAmount = Payment::where('status', 'unpaid')
            ->type($type)
            ->sum('nominal');
        $unpaidCount = Payment::where('status', 'unpaid')
            ->type($type)
            ->count();

        $dataTable->unpaidAmount = $unpaidAmount;
        $dataTable->unpaidCount = $unpaidCount;
        $dataTable->type = $type;

        return $dataTable
            ->render('dashboard.accounting.payment.index', $this->data);
    }

    public function update(string $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->status = 'paid';
            $payment->save();

            $type = $payment->type;

            return (new SuccessResource('Berhasil mengubah '.$type.' menjadi lunas', $payment))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Pembayaran tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
