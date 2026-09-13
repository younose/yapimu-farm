<?php

namespace App\DataTables;

use App\Models\JaminanJournal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class JaminanJournalDataTable extends DataTable
{
    public $debit;

    public $credit;

    public $saldo;

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'dashboard.jaminan-journal.action')
            ->editColumn('date', function ($journal) {
                return Carbon::parse($journal->date)->format('d M Y');
            })
            ->editColumn('debit', function ($journal) {
                return idrFormat($journal->debit);
            })
            ->editColumn('credit', function ($journal) {
                return idrFormat($journal->credit);
            })
            ->rawColumns([
                'action',
                'date',
                'debit',
                'credit',
            ])
            ->setRowId('jaminan_journals.id')
            ->with([
                'debit' => $this->debit,
                'credit' => $this->credit,
                'saldo' => $this->saldo,
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(JaminanJournal $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('jaminan-journal-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
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
                ->width(100)
                ->addClass('text-center')
                ->title('Aksi'),
            Column::make('date')
                ->addClass('my-auto')
                ->title('Tanggal'),
            Column::make('description')
                ->addClass('my-auto')
                ->title('Deskripsi'),
            Column::make('debit')
                ->addClass('my-auto')
                ->title('Jaminan Masuk'),
            Column::make('credit')
                ->addClass('my-auto')
                ->title('Jaminan Keluar'),
        ];
    }
}
