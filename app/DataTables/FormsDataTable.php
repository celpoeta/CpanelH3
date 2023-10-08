<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Facades\UtilityFacades;
use App\Models\Form;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;

class FormsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function (Form $form) {
                $st = '';
                if ($form->is_active == 1) {
                    $st = '<div class="form-check form-switch">
                            <input class="form-check-input chnageStatus" checked type="checkbox" role="switch" id="' . $form->id . '" data-url="' . route('form.status', $form->id) . '">
                            </div>';
                } else {
                    $st = '<div class="form-check form-switch">
                            <input class="form-check-input chnageStatus" type="checkbox" role="switch" id="' . $form->id . '" data-url="' . route('form.status', $form->id) . '">
                        </div>';
                }
                return $st;
            })
            ->addColumn('action', function (Form $form) {
                $hashids = new Hashids();
                return view('form.action', compact('form', 'hashids'));
            })->editColumn('created_at', function (Form $form) {
                return UtilityFacades::date_time_format($form->created_at->format('Y-m-d h:i:s'));
            })
            ->rawColumns(['status', 'location', 'action']);
    }
    public function query(Form $model)
    {
        $usr = \Auth::user();
        $role_id = $usr->roles->first()->id;
        $user_id = $usr->id;
        if ($usr->type == 'Admin') {
            return $model->newQuery();
        } else {
            return $model->newQuery()
                ->where(function ($query) use ($role_id, $user_id) {
                    $query->whereIn('id', function ($query1) use ($role_id) {
                        $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $role_id);
                    })->OrWhereIn('id', function ($query1) use ($user_id) {
                        $query1->select('form_id')->from('assign_forms_users')->where('user_id', $user_id);
                    });
                });
        }
        return $model;
    }

    public function html()
    {
        $dataTable =  $this->builder()
            ->setTableId('forms-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "paginate" => [
                    "next" => '<i class="ti ti-chevron-right"></i>',
                    "previous" => '<i class="ti ti-chevron-left"></i>'
                ],
                'lengthMenu' => __("_MENU_") . __('Entries Per Page'),
                "searchPlaceholder" => __('Search...'), "search" => "",
                "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')

            ])
            ->initComplete('function() {
                var table = this;
                var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                searchInput.removeClass(\'form-control form-control-sm\');
                searchInput.addClass(\'dataTable-input\');
                var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
            }');



        $canCreateForm = \Auth::user()->can('create-form');
        $canExportForm = \Auth::user()->can('export-form');

        $buttonsConfig = [];

        if($canCreateForm){
            $buttonsConfig[] =  [
                'extend' => 'create',
                'className' => 'btn btn-light-primary no-corner me-1 add_module',
                'action' => "function ( e, dt, node, config ) { window.location = '" . route('forms.create') . "'; }",
            ];
        }

        $exportButtonConfig = [];

        if($canExportForm){
            $exportButtonConfig = [
                'extend' => 'collection',
                'className' => 'btn btn-light-secondary me-1 dropdown-toggle',
                'text' => '<i class="ti ti-download"></i> ' . __('Export'),
                "buttons" => [
                    [
                        "extend" => "print",
                        "text" => '<i class="fas fa-print"></i> ' . __('Print'),
                        "className" => "btn btn-light text-primary dropdown-item",
                        "exportOptions" => ["columns" => [0, 1, 3]]
                    ],[
                        "extend" => "csv",
                        "text" => '<i class="fas fa-file-csv"></i> ' . __('CSV'),
                        "className" => "btn btn-light text-primary dropdown-item",
                        "exportOptions" => ["columns" => [0, 1, 3]]
                    ],[
                        "extend" => "excel",
                        "text" => '<i class="fas fa-file-excel"></i> ' . __('Excel'),
                        "className" => "btn btn-light text-primary dropdown-item",
                        "exportOptions" => ["columns" => [0, 1, 3]]
                    ],
                    //["extend" => "pdf", "text" => '<i class="fas fa-file-pdf"></i> ' . __('PDF'), "className" => "btn btn-light text-primary dropdown-item", "exportOptions" => ["columns" => [0, 1, 3]]],
                    [
                        "extend" => "copy",
                        "text" => '<i class="fas fa-copy"></i> ' . __('Copy'),
                        "className" => "btn btn-light text-primary dropdown-item",
                        "exportOptions" => ["columns" => [0, 1, 3]]
                    ],
                ]
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

    protected function getColumns()
    {
        return [
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false),
            Column::make('title')->title(__('Title')),
            Column::make('status')->title(__('Status')),
            Column::make('created_at')->title(__('Created At')),
            Column::computed('action')->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Forms_' . date('YmdHis');
    }
}
