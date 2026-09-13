<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WithdrawDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $balance = 0;
        $pending = 0;

        $table = (new EloquentDataTable($query));

        if (auth()->user()->hasRole('investor')) {
            $balance = auth()->user()->balance;
            $pending = Withdraw::pending()
                ->where('user_id', auth()->id())
                ->count();

            $table = $table->addColumn('action', function ($data) {
                if ($data->status === 'pending') {
                    $printRoute = route('withdraws.print', $data->id);

                    return "<a href='{$printRoute}' target='_blank'
                     class='btn btn-xs btn-warning'><i class='fa fa-print fa-fw'></i> Cetak Pengajuan</a>";
                }

                return "<a href='javascript:void(0)' onclick='openDetail({$data->id})' class='btn btn-xs btn-primary'><i class='fa fa-eye fa-fw'></i> Detail</a>";
            });
        } else {
            $balance = User::getInvestorBalances();
            $pending = Withdraw::pending()->count();

            $table = $table->addColumn('action', 'dashboard.dividend.withdraw.action');
            $table = $table->editColumn('user.name', function ($data) {
                return '<div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
                <img width="40" src="'.$data->user->photo_url.'" class="avatar-image avatar-sm rounded-circle" alt="photo" onerror="this.onerror=null;this.src=\''.asset('images/avatar/1.png').'\'">
            </div>
            <div class="d-flex flex-column">
                <span class="fw-bold">'.$data->user->name.'</span>
                <span class="small text-muted">'.$data->user->email.'</span>
            </div>
            </div>';
            });
        }

        $table = $table->editColumn('amount', function ($data) {
            return idrFormat($data->amount);
        })
            ->editColumn('status', function ($data) {
                $status = $data->getStatus();

                return "<span class='badge badge-pill {$status->bg} {$status->color}'>{$status->label}</span>";
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d M Y');
            })
            ->editColumn('transfer_type', function ($data) {
                return $data->transfer_type === 'cash' ? 'Tunai' : 'Transfer Bank';
            })
            ->rawColumns([
                'action',
                'user.name',
                'status',
            ])
            ->setRowId('id')
            ->with([
                'balance' => $balance,
                'pending' => $pending,
            ]);

        return $table;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Withdraw $model): QueryBuilder
    {
        return $model->newQuery()
            ->with([
                'user',
            ])
            ->when(auth()->user()->hasRole('investor'), function ($query) {
                return $query->where('user_id', auth()->id());
            });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('withdraw-table')
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
                ->title('Action')
                ->width('80px')
                ->addClass('text-center')
                ->orderable(false)
                ->searchable(false),
            Column::make('created_at')
                ->title('Tanggal')
                ->addClass('my-auto'),
            Column::make('user.name')
                ->title('Investor')
                ->addClass('my-auto'),
            Column::make('amount')
                ->title('Jumlah')
                ->addClass('my-auto'),
            Column::make('transfer_type')
                ->title('Metode Penarikan')
                ->addClass('my-auto'),
            Column::make('status')
                ->title('Status')
                ->addClass('my-auto'),
        ];

        if (auth()->user()->hasRole('investor')) {
            unset($columns[2]);
        }

        return $columns;
    }
}
