@extends('layouts.main')
@section('title', __('Permissions'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Permissions') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('roles.index'), __('Roles'), []) !!}</li>
            <li class="breadcrumb-item active">@yield('title')</li>
        </ul>
    </div>
@endsection
@section('content')
        <div class="row">
            <div class="mx-auto col-md-12">
                <div class="card responsive-card">
                    <div class="card-header">
                        <h5>{{ __('All Permissions') }}</h5>
                    </div>
                    {!! Form::open([
                        'route' => ['roles_permit', $role->id],
                        'method' => 'Post',
                        'class' => 'form-horizontal',
                        'data-validate',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="card-body responive-body">
                        <table class="table table-flush permission-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Module') }}</th>
                                    <th>{{ __('Permissions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="mb-2">
                                @foreach ($allmodules as $row)
                                    @if ($row != 'module')
                                        <tr>
                                            <td> {{ __(ucfirst($row)) }}</td>
                                            <td>
                                                <div class="row">
                                                    <?php
                                                    $default_permissions = ['manage', 'create', 'edit', 'delete', 'view', 'impersonate', 'fill', 'design', 'show', 'download', 'duplicate','theme-setting', 'integration', 'payment', 'result', 'vote' ,'export' , 'dashboard-qrcode','phoneverified','emailverified','document-generate'];
                                                    ?>
                                                    @foreach ($default_permissions as $permission)
                                                        @if (in_array($permission . '-' . $row, $allpermissions))
                                                            @php($key = array_search($permission . '-' . $row, $allpermissions))
                                                            <div class="col-3 form-check">
                                                                {{ Form::checkbox('permissions[]', $key, in_array($permission . '-' . $row, $permissions), ['class' => 'form-check-input', 'id' => 'permission_' . $key]) }}
                                                                {{ Form::label('permission_' . $key, ucfirst($permission), ['class' => 'form-check-label']) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="text-end">
                            {!! Html::link(route('roles.index'), __('Cancel'), [
                                'class' => 'btn btn-secondary',
                            ]) !!}
                            {!! Form::button(__('Save'), ['type' => 'submit','class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
    </div>
@endsection
