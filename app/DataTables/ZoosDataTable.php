<?php

namespace App\DataTables;

use App\Facades\UtilityFacades;
use App\Models\Zoo;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Storage;

class ZoosDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($request) {
                return UtilityFacades::date_time_format($request->created_at);
            })
             ->editColumn('status', function (Zoo $zoo) {
                if ($zoo->status == 1) {
                    return '<div class="form-check form-switch">
                                            <input class="form-check-input chnageStatus" checked type="checkbox" role="switch" id="' . $zoo->id . '" data-url="' . route('zoos.status', $zoo->id) . '">
                                        </div>';
                } else {
                    return '<div class="form-check form-switch">
                                             <input class="form-check-input chnageStatus" type="checkbox" role="switch" id="' . $zoo->id . '" data-url="' . route('zoos.status', $zoo->id) . '">
                                        </div>';
                }
            })
            ->editColumn("images", function (Zoo $blog) {
                if ($blog->url_image) {
                    $return = "<img src='" . $blog->url_image . "' width='100' />";
                } else {
                    $return = "<img src='" . Storage::url('test-image/350x250.png') . "' width='50' />";
                }
                return $return;
            })
            // ->addColumn('role', function (User $user) {
            //     $out = '';
            //     $out = '<span class="p-2 px-3 badge rounded-pill bg-primary">' . $user->type . '</span>';
            //     return $out;
            // })
            // ->addColumn('email_verified_at', function (User $user) {
            //     if ($user->email_verified_at) {
            //         $out = '<span class="p-2 px-3 badge rounded-pill bg-info">' . __('Verified') . '</span>';
            //         return $out;
            //     } else {
            //         $out = '<span class="p-2 px-3 badge rounded-pill bg-warning">' . __('Unverified') . '</span>';
            //         return $out;
            //     }
            //})
            ->addColumn('action', function (Zoo $user) {
                return view('zoos.action', compact('user'));
            })
            ->rawColumns(['status','images','action']);
    }

    public function query(Zoo $model): QueryBuilder
    {
        return $model->newQuery()->join('blog_categories','category_id', '=', 'blog_categories.id')
        ->select('zoos.*', 'blog_categories.name as category_name')->orderBy('id', 'ASC');
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
        ->setTableId('users-table')
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->orderBy(1)
        ->language([
            "paginate" => [
                "next" => '<i class="ti ti-chevron-right"></i>',
                "previous" => '<i class="ti ti-chevron-left"></i>'
            ],
            'lengthMenu' => __("_MENU_") . __('Entries Per Page'),
            "searchPlaceholder" => __('Search...'),
            "search" => "",
            "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')
        ])
        ->initComplete('function() {
    var table = this;
    var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
    searchInput.removeClass(\'form-control form-control-sm\');
    searchInput.addClass(\'dataTable-input\');
    var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
}');

    $canCreatePoll = \Auth::user()->can('create-user');
    $canExportPoll = \Auth::user()->can('export-user');
    $buttonsConfig = [];


    if ($canCreatePoll) {
        $buttonsConfig[] = [
            'extend' => 'create',
            'className' => 'btn btn-light-primary no-corner me-1 add_zoo',
            'action' => " function (e, dt, node, config) { }",
        ];
    }
    $exportButtonConfig = [];

    if ($canExportPoll) {
        $exportButtonConfig = [
            'extend' => 'collection',
            'className' => 'btn btn-light-secondary me-1 dropdown-toggle',
            'text' => '<i class="ti ti-download"></i> ' . __('Export'),
            'buttons' => [
                [
                    'extend' => 'print',
                    'text' => '<i class="fas fa-print"></i> ' . __('Print'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'csv',
                    'text' => '<i class="fas fa-file-csv"></i> ' . __('CSV'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'excel',
                    'text' => '<i class="fas fa-file-excel"></i> ' . __('Excel'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
            ],
        ];
    }


    $buttonsConfig = array_merge($buttonsConfig, [
        $exportButtonConfig,
        [
            'extend' => 'reset',
            'className' => 'btn btn-light-danger me-1',
        ],
        [
            'extend' => 'reload',
            'className' => 'btn btn-light-warning',
        ],
    ]);

    $dataTable->parameters([
        "dom" =>  "
    <'dataTable-top row'<'dataTable-dropdown page-dropdown col-lg-2 col-sm-12'l><'dataTable-botton table-btn col-lg-6 col-sm-12'B><'dataTable-search tb-search col-lg-3 col-sm-12'f>>
    <'dataTable-container'<'col-sm-12'tr>>
    <'dataTable-bottom row'<'col-sm-5'i><'col-sm-7'p>>
",
        'buttons' => $buttonsConfig,
    ]);

    $dataTable->language([
        'buttons' => [
            'create' => __('Create'),
            'export' => __('Export'),
            'print' => __('Print'),
            'reset' => __('Reset'),
            'reload' => __('Reload'),
            'excel' => __('Excel'),
            'csv' => __('CSV'),
        ]
    ]);

    return $dataTable;
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('scientific_name')->title("Nombre cientifico"),
            Column::make('common_name')->title(__('Nombre comun')),
            Column::make('images')->title(__('IMAGEN')),
            Column::make('risk')->title('en extinción'),
            Column::make('category_name')->title(__('Category')),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::computed('action')->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Zoos_' . date('YmdHis');
    }
}
