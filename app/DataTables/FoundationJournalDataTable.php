<?php

namespace App\DataTables;

use App\Models\FoundationJournal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FoundationJournalDataTable extends DataTable
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
        $table = (new EloquentDataTable($query));
        $table = $table->addColumn('action', 'dashboard.accounting.foundation-journal.action');
        $table = $table->editColumn('date', function ($journal) {
            return Carbon::parse($journal->date)->format('d M Y');
        })
            ->editColumn('debit', function ($journal) {
                return idrFormat($journal->debit);
            })
            ->editColumn('credit', function ($journal) {
                return idrFormat($journal->credit);
            })
            ->editColumn('proof', function ($journal) {
                return $journal->proof_url ? '<a class="btn btn-warning btn-xs" href="'.$journal->proof_url.'" target="_blank"><i class="fas fa-external-link-alt fa-fw"></i> Lihat</a>' : '-';
            })
            ->rawColumns([
                'action',
                'date',
                'debit',
                'credit',
                'proof',
            ])
            ->setRowId('jornals.id')
            ->with([
                'debit' => $this->debit,
                'credit' => $this->credit,
                'saldo' => $this->saldo,
                'period' => $this->currentPeriod,
            ]);

        return $table;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FoundationJournal $model): QueryBuilder
    {
        return $model
            ->when(request('start_date') && request('end_date'), function ($query) {
                $query->whereBetween('date', [request('start_date'), request('end_date')]);
            })
            ->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('journal-table')
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
        $columns = [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
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
                ->title('Masuk'),
            Column::make('credit')
                ->addClass('my-auto')
                ->title('Keluar'),
            Column::make('proof')
                ->addClass('my-auto')
                ->searchable(false)
                ->orderable(false)
                ->title('Bukti'),
        ];

        return $columns;
    }
}
