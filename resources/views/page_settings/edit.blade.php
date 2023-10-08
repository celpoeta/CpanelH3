@extends('layouts.main')
@section('title', __('Landing Page'))
@section('breadcrumb')
<div class="col-md-12">
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Page Settings') }}</h4>
    </div>
    <ul class="breadcrumb">
        <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
        <li class="breadcrumb-item">{{ __('Edit Page') }}</li>
    </ul>
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-7 col-lg-7 mx-auto">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="card">
                            <div class="tab-pane fade show active" id="apps-setting" role="tabpanel"
                                aria-labelledby="landing-apps-setting">
                                {!! Form::model($page_settings, [
                                    'route' => ['page-setting.update', $page_settings->id],
                                    'method' => 'patch',
                                    'id' => 'froentend-form',
                                    'enctype' => 'multipart/form-data',
                                ]) !!}
                                @method('put')
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8 d-flex align-items-center">
                                            <h5 class="mb-0">{{ __('Page Setting') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {{ Form::label('page_title', __('Page Title'), ['class' => 'form-label']) }}
                                                *
                                                {!! Form::text('page_title', $page_settings->title, [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Enter Page Title',
                                                    'id' => 'page_title',
                                                ]) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{ Form::label('type', __('Select Type'), ['class' => 'form-label']) }}
                                                {!! Form::select(
                                                    'type',
                                                    ['link' => 'link', 'desc' => 'desc'],
                                                    $page_settings->type,
                                                    [
                                                        'class' => 'form-select',
                                                        'data-trigger',
                                                        'id' => 'type',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-12  {{ $page_settings->type == 'link' ? 'block' : 'd-none' }}" id="link">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('url_type', __('Link Type'), ['class' => 'form-label']) }}
                                                    {!! Form::select(
                                                        'url_type',
                                                        ['ifream' => 'ifream', 'internal link' => 'internal link', 'external link' => 'external link'],
                                                        $page_settings->url_type,
                                                        [
                                                            'class' => 'form-select',
                                                            'data-trigger',
                                                            'id' => 'link',
                                                        ],
                                                    ) !!}
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {{ Form::label('page_url', __('Page URL'), ['class' => 'form-label']) }}
                                                    {!! Form::text('page_url', $page_settings->page_url, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter Link URL'),
                                                    ]) !!}
                                                    <small class="text-muted"><b>Simple Page</b> :- Leave it
                                                        Blank</small><br>
                                                    <small class="text-muted"><b>Internal Link</b> :-http://localhost/Prime-Laravel-Form-Builder/main_file/</small><br>
                                                    <small class="text-muted"><b>External Link</b> :-
                                                        http://google.com/</small>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {{ Form::label('friendly_url', __('Search Friendly URL'), ['class' => 'form-label']) }}
                                                    {!! Form::text('friendly_url', $page_settings->friendly_url, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('Enter Search Friendly URL'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 {{ $page_settings->type == 'desc' ? 'block' : 'd-none' }}" id="description">
                                            <div class="form-group">
                                                {{ Form::label('description', __('Footer Sub menu Detail'), ['class' => 'form-label']) }}
                                                {!! Form::textarea('descriptions', $page_settings->description, [
                                                    'class' => 'form-control',
                                                    'rows' => '1',
                                                    'placeholder' => __('Enter Sub Menu detail'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        <a href="{{ route('page-setting.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        {{ Form::button(__('Save'), ['type' => 'submit', 'id' => 'save-btn', 'class' => 'btn btn-primary']) }}
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        CKEDITOR.replace('descriptions', {
            filebrowserUploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
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
        $("select[name='type']").change(function() {
            $('#link').hide();
            $('#description').hide();
            var test = $(this).val();
            if (test == 'link') {
                $('#description').hide();
                $('#link').show();
                $("#link").fadeIn(500);
                $("#link").removeClass('d-none');
                $('#description').fadeOut(500);
            } else {
                $('#link').hide();
                $('#description').show();
                $("#link").fadeOut(500);
                $("#description").fadeIn(500);
                $("#description").removeClass('d-none');
            }
        });
    </script>
@endpush
