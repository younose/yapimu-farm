<?php

namespace App\DataTables;

use App\Models\Dividend;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DividendReportDataTable extends DataTable
{
    public $currentPeriod;

    public $isInvestor = false;

    public function __construct()
    {
        $this->isInvestor = auth()->user()->hasRole('investor');
    }

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        if ($this->isInvestor) {
            return (new EloquentDataTable($query))
                ->editColumn('price_per_share', function ($dividend) {
                    return idrFormat($dividend->price_per_share);
                })
                ->editColumn('fund', function ($dividend) {
                    return idrFormat($dividend->fund);
                })
                ->editColumn('period_result', function ($dividend) {
                    return idrFormat($dividend->period_result);
                })
                ->editColumn('dividend', function ($dividend) {
                    return idrFormat($dividend->dividend);
                })
                ->editColumn('total_dividend', function ($dividend) {
                    return idrFormat($dividend->total_dividend);
                })
                ->editColumn('created_at', function ($dividend) {
                    return $dividend->created_at->format('d M Y');
                })
                ->rawColumns([
                    'created_at',
                ])
                ->setRowId('id')
                ->with([
                    'period' => $this->currentPeriod,
                ]);
        }

        return (new EloquentDataTable($query))
            ->editColumn('user.name', function ($dividend) {
                // user.photo_url and user.name
                return '<div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <img width="40" src="'.$dividend->user->photo_url.'" class="avatar-image avatar-sm rounded-circle" alt="photo" onerror="this.onerror=null;this.src=\''.asset('images/avatar/1.png').'\'">
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">'.$dividend->user->name.'</span>
                            <span class="small text-muted">'.$dividend->user->email.'</span>
                        </div>
                        </div>';
            })
            ->editColumn('price_per_share', function ($dividend) {
                return idrFormat($dividend->price_per_share);
            })
            ->editColumn('fund', function ($dividend) {
                return idrFormat($dividend->fund);
            })
            ->editColumn('period_result', function ($dividend) {
                return idrFormat($dividend->period_result);
            })
            ->editColumn('dividend', function ($dividend) {
                return idrFormat($dividend->dividend);
            })
            ->editColumn('total_dividend', function ($dividend) {
                return idrFormat($dividend->total_dividend);
            })
            ->rawColumns([
                'action',
                'user.name',
            ])
            ->setRowId('id')
            ->with([
                'period' => $this->currentPeriod,
            ]);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Dividend $model): QueryBuilder
    {
        if ($this->isInvestor) {
            return $model->newQuery()
                ->where('user_id', auth()->id())
                ->with([
                    'period',
                    'user',
                ]);
        }

        if ($this->currentPeriod) {
            return $model->newQuery()
                ->where('period_id', $this->currentPeriod->id)
                ->with([
                    'period',
                    'user',
                ]);
        }

        return $model->newQuery()
            ->where('dividends.id', 0)
            ->with([
                'period',
                'users',
            ]);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dividend-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy($this->isInvestor ? 0 : 1, 'desc')
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
        if ($this->isInvestor) {
            return [
                Column::make('created_at')
                    ->addClass('my-auto')
                    ->title('Tanggal'),
                Column::make('period.name')
                    ->addClass('my-auto')
                    ->title('Periode'),
                Column::make('shares')
                    ->addClass('my-auto')
                    ->title('Saham'),
                Column::make('period_result')
                    ->addClass('my-auto')
                    ->title('Hasil Periode'),
                Column::make('dividend')
                    ->addClass('my-auto')
                    ->title('Perlembar'),
                Column::make('total_dividend')
                    ->addClass('my-auto')
                    ->title('Bagi Hasil'),
            ];
        }

        return [
            Column::make('user.name')
                ->addClass('my-auto')
                ->title('Investor'),
            Column::make('shares')
                ->addClass('my-auto')
                ->title('Saham'),
            Column::make('period_result')
                ->addClass('my-auto')
                ->title('Hasil Periode'),
            Column::make('dividend')
                ->addClass('my-auto')
                ->title('Perlembar'),
            Column::make('total_dividend')
                ->addClass('my-auto')
                ->title('Bagi Hasil'),
        ];
    }
}
