<?php

namespace App\DataTables;

use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class JournalDataTable extends DataTable
{
    public $debit;

    public $credit;

    public $saldo;

    public $currentPeriod;

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $journalDebit = 0;
        $journalCredit = 0;
        $journalBalance = 0;

        $table = (new EloquentDataTable($query));

        if (! auth()->user()->hasRole('investor')) {
            $table = $table->addColumn('action', 'dashboard.accounting.journal.action');
        }

        $table = $table->editColumn('date', function ($journal) {
            return Carbon::parse($journal->date)->format('d M Y');
        })
            ->editColumn('debit', function ($journal) {
                return idrFormat($journal->debit);
            })
            ->editColumn('credit', function ($journal) {
                return idrFormat($journal->credit);
            })
        // bukti
            ->editColumn('proof', function ($journal) {
                return $journal->proof_url ? '<a class="btn btn-warning btn-xs" href="'.$journal->proof_url.'" target="_blank"><i class="fas fa-external-link-alt fa-fw"></i> Lihat</a>' : '-';
            })
        // ->editColumn('balance', function ($journal) use (&$journalDebit, &$journalCredit, &$journalBalance) {
        //     $journalDebit += $journal->debit;
        //     $journalCredit += $journal->credit;
        //     $journalBalance = $journalDebit - $journalCredit;

        //     return number_format($journalBalance);

        // })
            ->rawColumns([
                'action',
                'date',
                'debit',
                'credit',
                'proof',
                // 'balance',
            ])
            ->setRowId('jornals.id')
        // Additional Data Response
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
    public function query(Journal $model): QueryBuilder
    {
        if ($this->currentPeriod) {
            return $model->newQuery()
                ->where('period_id', $this->currentPeriod->id);
            // ->with('period');
        }

        return $model->newQuery()
            ->where('journals.id', 0);
        // ->with('period');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $orderIndex = 1;
        if (auth()->user()->hasRole('investor')) {
            $orderIndex = 0;
        }

        return $this->builder()
            ->setTableId('journal-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy($orderIndex, 'desc')
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
            // Column::make('period.name')
            //     ->addClass('my-auto')
            //     ->title('Periode'),
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
            // Column::make('balance')
            //     ->addClass('my-auto')
            //     ->title('Saldo'),

        ];

        if (auth()->user()->hasRole('investor')) {
            unset($columns[0]);
        }

        return $columns;
    }
}
