<?php

namespace App\Http\Controllers\Dashboard\Dividend;

use App\DataTables\WithdrawDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\Withdraw\WithdrawCreateRequest;
use App\Http\Requests\Withdraw\WithdrawUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;

class WithdrawController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Penarikan Dividen');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Dividen', '#');
        $this->addBreadcrumb($this->getTitle(), '#');
    }

    public function index(WithdrawDataTable $dataTable)
    {
        $this->data['bankAccount'] = BankAccount::where('user_id', auth()->id())->get();

        return $dataTable
            ->render('dashboard.dividend.withdraw.index', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WithdrawCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();
            $validated['status'] = 'pending';
            // $validated['description'] = 'credit';
            $withdraw = Withdraw::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil mengajukan penarikan baru', $withdraw))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil mengajukan penarikan baru');
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $withdraw = Withdraw::with([
                'user',
                'processedBy',
                'bankAccount'])->findOrFail($id);

            $withdraw->status_detail = $withdraw->getStatus();

            return (new SuccessResource('Berhasil mengambil data penarikan', $withdraw))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Data penarikan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WithdrawUpdateRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $status = $validated['withdrawStatus'] == 'terima' ? 'approved' : 'rejected';
            $note = $validated['withdrawStatus'] == 'terima' ? $request->noteApprove : $request->noteReject;

            $withdraw = Withdraw::findOrFail($id);
            $withdraw->update([
                'status' => $status,
                'note' => $note,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            if ($status == 'approved') {
                $user = User::findOrFail($withdraw->user_id);
                $balance = $user->balance;
                $nowBalance = $balance - $withdraw->amount;

                Transaction::create([
                    'user_id' => $withdraw->user_id,
                    'reference_id' => $withdraw->id,
                    'description' => 'Penarikan Dividen',
                    'type' => 'credit',
                    'amount' => $withdraw->amount,
                ]);

                $user->update([
                    'balance' => $nowBalance,
                ]);
            }

            DB::commit();

            return (new SuccessResource('Berhasil mengubah status penarikan', $withdraw))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Data penarikan tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Throwable $th) {
            DB::rollBack();

            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }

    public function printWithdrawRequest($id)
    {

        $withdraw = Withdraw::with([
            'user',
            'processedBy',
            'bankAccount'])->findOrFail($id);

        $withdraw->status_detail = $withdraw->getStatus();
        $this->setTitle('Cetak Pengajuan Penarikan');
        $this->setData('withdraw', $withdraw);

        return view('dashboard.dividend.withdraw.print', $this->data);
    }
}
