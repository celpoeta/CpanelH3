@extends('layouts.main')
@section('title', __('Categories'))
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('Blog Categories') }}</h4>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active">
                            {!! Html::link(route('home'), __('Dashboard'), ['']) !!}
                        </li>
                        <li class="breadcrumb-item active">{{ __('Blog Categories') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    @include('layouts.includes.datatable_css')
@endpush
@push('script')
    @include('layouts.includes.datatable_js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            $('.custom_select').select2();
        });
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'This is a placeholder set in the config',
                    searchPlaceholderValue: 'This is a search placeholder',
                });
            }
        });
    </script>
@endpush
