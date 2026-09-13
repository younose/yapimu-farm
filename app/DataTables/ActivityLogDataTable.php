<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActivityLogDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $events = $this->getActivityEvents();

        return (new EloquentDataTable($query))
            ->addColumn('action', function ($data) {
                return '<a href="#" class="btn btn-xs btn-primary" onclick="showDetail('.$data->id.')">
                 <i class="fas fa-eye fa-fw"></i>
                </a>';
            })
            ->editColumn('event', function ($data) use ($events) {
                $event = $events->{$data->event};

                return "<span class='badge {$event->bg} {$event->color}'>
                    <i class='{$event->icon}'></i>
                    {$event->label}
                </span>";
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d M Y H:i:s');
            })
            ->rawColumns(['action', 'event'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Activity $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('causer')
            ->with('subject');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('activity-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
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
            Column::make('created_at')
                ->title('Tanggal'),
            Column::make('causer.name')
                ->title('Pengguna'),
            Column::make('log_name')
                ->title('Jenis'),
            Column::make('event')
                ->title('Event')
                ->addClass('my-auto'),
        ];
    }

    public function getActivityEvents()
    {
        return (object) [
            'created' => (object) [
                'label' => 'CREATED',
                'bg' => 'bg-success',
                'color' => 'text-white',
                'icon' => 'fas fa-plus fa-fw',
            ],
            'updated' => (object) [
                'label' => 'UPDATED',
                'bg' => 'bg-primary',
                'color' => 'text-white',
                'icon' => 'fas fa-edit fa-fw',
            ],
            'deleted' => (object) [
                'label' => 'DELETED',
                'bg' => 'bg-danger',
                'color' => 'text-white',
                'icon' => 'fas fa-trash fa-fw',
            ],
        ];
    }
}
