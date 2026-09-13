<?php

namespace App\DataTables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Number;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DatabaseBackupDataTable extends DataTable
{
    // datatable from collection
    public function dataTable($query)
    {
        return datatables()
            ->collection($query)
            ->addColumn('action', function ($query) {
                return view('dashboard.misc.database-backup.action', $query)->render();
            })
            ->editColumn('size', function ($query) {
                return Number::fileSize((float) $query['size'] * 1024);
            })
            ->editColumn('date', function ($query) {
                return Carbon::parse($query['date'])->format('d M Y H:i:s');
            })
            ->rawColumns(['action']);
    }

    public function query()
    {
        Artisan::call('snapshot:list');
        $snapshot_data = Artisan::output();

        $snapshot_data = array_filter(explode("\n", $snapshot_data));

        $snapshot_list = new Collection();
        if (count($snapshot_data) != 1) {
            foreach ($snapshot_data as $index => $snapshot_list_row) {
                if ($snapshot_list_row[0] != '+') {
                    if ($index != 1) {
                        $snapshot_list_column = array_map('trim', array_filter(explode('|', $snapshot_list_row)));
                        $file_name = $snapshot_list_column[1];
                        $name = explode('_', $snapshot_list_column[1]);
                        $date = $snapshot_list_column[2];
                        $size = $snapshot_list_column[3];

                        $snapshot_list->push([
                            'name' => $name[0],
                            'date' => $date,
                            'size' => $size,
                        ]);
                    }
                }
            }
        }

        return $snapshot_list;

    }

    public function html()
    {
        return $this->builder()
            ->setTableId('database-backups-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'desc')
            ->selectStyleSingle();
    }

    protected function getColumns()
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
                ->title('Nama File'),
            Column::make('date')
                ->addClass('my-auto')
                ->title('Tanggal'),
            Column::make('size')
                ->addClass('my-auto')
                ->title('Ukuran'),
        ];
    }
}
