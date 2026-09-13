<?php

namespace App\DataTables;

use App\Models\Period;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PeriodDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $status = Period::getStatuses();

        return (new EloquentDataTable($query))
            ->addColumn('action', 'dashboard.period.action')
            ->editColumn('status', function ($period) use ($status) {
                return "<span class='badge {$status->{$period->status}->bg} {$status->{$period->status}->color}'>
                <i class='{$status->{$period->status}->icon}'></i>
                {$status->{$period->status}->label}
                </span>";
            })
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Period $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('period-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'desc')
            ->selectStyleSingle();
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
                ->title('Periode'),
            Column::make('start_date')
                ->addClass('my-auto')
                ->title('Tanggal Mulai'),
            Column::make('end_date')
                ->addClass('my-auto')
                ->title('Tanggal Selesai'),
            Column::make('status')
                ->addClass('my-auto')
                ->title('Status'),
        ];
    }
}
