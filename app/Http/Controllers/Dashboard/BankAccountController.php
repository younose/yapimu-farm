<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\BankAccountDataTable;
use App\Http\Controllers\_core\DashboardController;
use App\Http\Requests\BankAccount\BankAccountCreateRequest;
use App\Http\Requests\BankAccount\BankAccountUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\BankAccount;
use Spatie\Activitylog\Models\Activity;

class BankAccountController extends DashboardController
{
    public function __construct()
    {
        $this->setTitle('Akun Bank');
        $this->addBreadcrumb('Dashboard', route('dashboard'));
        $this->addBreadcrumb('Akun Bank', '#');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BankAccountDataTable $dataTable)
    {
        $typeBank = [
            'Bank Rakyat Indonesia (BRI)',
            'Bank Mandiri',
            'Bank Central Asia (BCA)',
            'Bank Negara Indonesia (BNI)',
            'Bank CIMB Niaga',
            'Bank Danamon',
            'Bank Tabungan Negara (BTN)',
            'Bank Permata',
            'Bank Mega',
            'Bank OCBC NISP',
            'Bank Maybank Indonesia',
            'Bank HSBC Indonesia',
            'Bank UOB Indonesia',
            'Bank Panin',
            'Bank Bukopin',
            'Bank BTPN',
            'Bank Sinarmas',
            'Bank Mestika',
            'Bank DBS Indonesia',
            'Bank Artha Graha Internasional',
            'Bank BRI Syariah',
            'Bank Mandiri Syariah',
            'Bank BCA Syariah',
            'Bank Mega Syariah',
            'Bank BNI Syariah',
            'Bank Muamalat Indonesia',
            'Bank BJB',
            'Bank Kaltimtara',
            'Bank Papua',
            'Bank Nusa Tenggara Timur (NTT)',
            'Bank Sulawesi',
            'Bank Maluku Malut',
            'Bank Jatim',
            'Bank Riau Kepri',
            'Bank Sumut',
            'Bank Lampung',
            'Bank Sumbar',
            'Bank Sumsel Babel',
            'Bank Jateng',
            'Bank Jabar Banten',
            'Bank DKI',
            'Bank Jatim',
            'Bank Aceh',
            'Bank Kalsel',
            'Bank Kalteng',
            'Bank Kaltara',
            'Bank Bali',
            'Bank Jateng',
            'Bank Sulselbar',
            'Bank Sultra',
            'Bank Kalbar',
            'Bank SulutGo',
            'Bank Maluku Malut',
            'Bank Gorontalo',
            'Bank Jambi',
            'Bank Bengkulu',
            'Bank NTT',
            'Bank Sulteng',
            'Bank Sultra',
            'Bank Sulbar',
            'Bank Papua',
            'Bank Banten',
            'Bank Sumbar',
            'Bank NTB',
            'Bank Riau Kepri',
            'Bank SulutGo',
            'Bank DIY',
            'Bank Lampung',
            'Bank Jatim',
            'Bank Kalteng',
            'Bank SumselBabel',
            'Bank Maluku Malut',
            'Bank Jateng',
            'Bank Papua',
            'Bank DKI Jakarta',
        ];

        sort($typeBank);

        $this->data['bankAccount'] = BankAccount::where('user_id', auth()->id())->get();
        $this->data['typeBank'] = $typeBank;

        return $dataTable
            ->render('dashboard.bank-account.index', $this->data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);

            return (new SuccessResource('Berhasil mengambil data akun bank', $bankAccount))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Akun bank tidak ditemukan'))
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
    public function store(BankAccountCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            $bankAccount = BankAccount::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return (new SuccessResource('Berhasil menambah akun bank baru', $bankAccount))
                    ->response()
                    ->setStatusCode(201);
            }

            return back()->with('success', 'Berhasil menambah akun bank baru');
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
    public function update(BankAccountUpdateRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            $bankAccount = BankAccount::findOrFail($id);
            $bankAccount->update($validated);

            return (new SuccessResource('Berhasil mengubah akun bank', $bankAccount))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Akun bank tidak ditemukan'))
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
            $accountBank = BankAccount::findOrFail($id);
            $activity = Activity::where('subject_id', $id)
                ->where('subject_type', BankAccount::class)
                ->get();
            $activity->each->delete();
            $accountBank->delete();

            return (new SuccessResource('Berhasil menghapus akun bank', $accountBank))
                ->response()
                ->setStatusCode(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return (new ErrorResource($th, 'Akun bank tidak ditemukan'))
                ->response()
                ->setStatusCode(404);
        } catch (\Illuminate\Database\QueryException $th) {
            return (new ErrorResource($th, 'Akun bank tidak bisa dihapus karena terkait dengan data lain'))
                ->response()
                ->setStatusCode(400);
        } catch (\Throwable $th) {
            return (new ErrorResource($th, 'Terjadi kesalahan!'))
                ->response()
                ->setStatusCode(500);
        }
    }
}
