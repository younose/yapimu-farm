<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvestorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $totalShares = getSetting('default_share_count');
        $sharesPrice = getSetting('default_share_price');
        $aqumulatedShares = User::investors()->sum('shares');

        return (new EloquentDataTable($query))
            ->addColumn('action', 'dashboard.investor.action')
            // photo
            ->editColumn('name', function ($user) {
                // user.photo_url and user.name
                return '<div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <img width="40" src="'.$user->photo_url.'" class="avatar-image avatar-sm rounded-circle" alt="photo" onerror="this.onerror=null;this.src=\''.asset('images/avatar/1.png').'\'">
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold">'.$user->name.'</span>
                    </div>
                    </div>';
            })
            ->editColumn('shares', function ($user) {
                return $user->shares;
            })
            ->editColumn('balance', function ($user) {
                return idrFormat($user->balance);
            })
            ->rawColumns(['action', 'name'])
            ->setRowId('id')
            ->with([
                'totalShares' => $totalShares,
                'sharesPrice' => $sharesPrice,
                'aqumulatedShares' => $aqumulatedShares,
                'investorBalance' => User::investors()->sum('balance'),
                'investorCount' => User::investors()->count(),
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->investors();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('investor-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
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
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->title('Aksi'),
            Column::make('name')
                ->addClass('my-auto')
                ->title('Nama'),
            Column::make('email')
                ->addClass('my-auto')
                ->title('Email'),
            Column::make('shares')
                ->addClass('my-auto')
                ->title('Saham'),
            Column::make('balance')
                ->addClass('my-auto')
                ->title('Saldo'),
        ];
    }
}
