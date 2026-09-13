<?php

namespace App\DataTables;

use App\Models\ClosingPeriod;
use App\Models\Period;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PeriodicReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($closing) {
                return '<a href="javascript:void(0);" onclick="detail('.$closing->id.')" id="closing-'.$closing->id.'"
                 class="btn btn-xs btn-primary" data-toggle="tooltip" title="Detail">
                    <i class="fas fa-eye fa-fw"></i>
                    Detail
                 </a>';
            })
            ->editColumn('period.name', function ($closing) {
                return '<i class="fa-solid fa-calendar-check text-primary me-1"></i> '.e($closing->period->name);
            })
            ->editColumn('net_profit', function ($closing) {
                return idrFormat($closing->net_profit);
            })
            ->editColumn('management_fee', function ($closing) {
                return idrFormat($closing->management_fee);
            })
            ->editColumn('cooperative_fee', function ($closing) {
                return idrFormat($closing->cooperative_fee);
            })
            ->editColumn('zakat', function ($closing) {
                return idrFormat($closing->zakat);
            })
            ->editColumn('dividend', function ($closing) {
                return idrFormat($closing->dividend);
            })
            ->editColumn('dividend_foundation', function ($closing) {
                return idrFormat($closing->dividend_foundation);
            })
            ->editColumn('dividend_investor', function ($closing) {
                return idrFormat($closing->dividend_investor);
            })
            ->orderColumn('period.name', function ($query, $order) {
                $query->orderBy(
                    Period::select('start_date')->whereColumn('periods.id', 'closing_periods.period_id'),
                    $order
                );
            })
            ->rawColumns([
                'action',
                'period.name',
            ])
            ->setRowId('closing_periods.id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ClosingPeriod $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('closing_periods.*')
            ->with(['period']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('closing-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->drawCallback('function() {
                if (typeof onClosingTableDraw === "function") {
                    onClosingTableDraw();
                }
            }');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('period.name')
                ->addClass('my-auto')
                ->title('Periode'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center')
                ->title('Detail'),
            Column::make('net_profit')
                ->addClass('my-auto')
                ->title('Sisa Hasil Panen'),
            Column::make('management_fee')
                ->addClass('my-auto')
                ->title('Manajemen'),
            Column::make('cooperative_fee')
                ->addClass('my-auto')
                ->title('Koperasi'),
            Column::make('zakat')
                ->addClass('my-auto')
                ->title('Zakat Mal'),
            Column::make('dividend')
                ->addClass('my-auto')
                ->title('Dividen'),
            Column::make('dividend_foundation')
                ->addClass('my-auto')
                ->title('Kandang Yayasan'),
            Column::make('dividend_investor')
                ->addClass('my-auto')
                ->title('Kandang Investor'),
        ];
    }
}
