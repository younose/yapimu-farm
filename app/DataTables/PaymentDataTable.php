<?php

namespace App\DataTables;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentDataTable extends DataTable
{
    public $type;

    public $unpaidAmount;

    public $unpaidCount;

    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $statuses = Payment::getStatuses();
        $table = (new EloquentDataTable($query));

        if (! auth()->user()->hasRole('investor')) {
            $table = $table->addColumn('action', function ($zakat) {
                if ($zakat->status === 'unpaid') {
                    return '<a href="javascript:void(0);" onclick="payZakat('.$zakat->id.')" id="pay-zakat-'.$zakat->id.'"
                     class="btn btn-xs btn-warning" data-toggle="tooltip" title="Bayar Zakat">
                        <i class="fas fa-money-bill-wave"></i>
                        Bayar
                     </a>';
                }

                return '*';
            });
        }

        $table = $table->editColumn('created_at', function ($zakat) {
            return Carbon::parse($zakat->created_at)->format('d M Y');
        })
            ->editColumn('nominal', function ($zakat) {
                return idrFormat($zakat->nominal);
            })
            ->editColumn('status', function ($zakat) use ($statuses) {
                $status = $statuses->{$zakat->status};

                return "<span class='badge {$status->bg} {$status->color}'><i class='{$status->icon}'></i> {$status->label}</span>";
            })
            ->rawColumns([
                'action',
                'status',
            ])
            ->setRowId('id')
            ->with([
                'unpaidAmount' => $this->unpaidAmount,
                'unpaidCount' => $this->unpaidCount,
            ]);

        return $table;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Payment $model): QueryBuilder
    {
        return $model->newQuery()
            ->type($this->type)
            ->with('period');
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
            ->setTableId('payment-table')
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
                ->width(100)
                ->addClass('text-center')
                ->title('Aksi'),
            Column::make('created_at')
                ->addClass('my-auto')
                ->title('Tanggal'),
            Column::make('period.name')
                ->addClass('my-auto')
                ->title('Periode'),
            Column::make('nominal')
                ->addClass('my-auto')
                ->title('Nominal'),
            Column::make('status')
                ->addClass('my-auto')
                ->title('Status'),
        ];

        if (auth()->user()->hasRole('investor')) {
            unset($columns[0]);
        }

        return $columns;
    }
}
