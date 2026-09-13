<?php

namespace App\DataTables;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransactionDataTable extends DataTable
{
    public $userId;

    public $user;

    public function __construct()
    {
        $this->userId = request()->user_id;
        if (!request()->user_id) {
            $authUser = auth()->user();
            if ($authUser->hasRole('investor')) {
                $this->userId = $authUser->id;
            }
        }

        $this->user = User::find($this->userId);
    }

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {

        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($transaction) {
                return $transaction->created_at->format('d M Y');
            })
            ->editColumn('amount', function ($transaction) {
                return idrFormat($transaction->amount);
            })
            ->editColumn('type', function ($transaction) {
                if ($transaction->type == 'debit') {
                    return '<span class="badge badge-success text-white"><i class="fas fa-arrow-down fa-fw"></i> Masuk</span>';
                }

                return '<span class="badge badge-danger text-white"><i class="fas fa-arrow-up fa-fw"></i> Keluar</span>';
            })
            ->rawColumns(['type'])
            ->setRowId('id')
            ->with([
                'user' => $this->user,
                'debit' => Transaction::where('user_id', $this->userId)->where('type', 'debit')->sum('amount'),
                'credit' => Transaction::where('user_id', $this->userId)->where('type', 'credit')->sum('amount'),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Transaction $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('user_id', $this->userId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('transaction-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->drawCallback('function() {
                renderWidget();
            }');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('created_at')
                ->addClass('my-auto')
                ->title('Tanggal')
                ->searchable(false),
            Column::make('description')
                ->addClass('my-auto')
                ->title('Deskripsi'),
            Column::make('type')
                ->addClass('my-auto')
                ->title('Tipe'),
            Column::make('amount')
                ->addClass('my-auto')
                ->title('Jumlah'),
        ];
    }
}
