@extends('layouts.main')
@section('title', 'View Forms')
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('View Forms') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('forms.index'), __('Forms'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('View Forms') }}</li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="section-body">
            <div class="row">
                @if (!empty($form_value->Form->logo))
                    <div class="text-center gallery gallery-md">
                        <img id="app-dark-logo" class="gallery-item float-none"
                            src="{{ Illuminate\Support\Facades\File::exists($form_value->Form->logo)
                                ? asset('storage/app/' . $form_value->Form->logo)
                                : Storage::url('appLogo/78x78.png') }}">
                    </div>
                @endif
                <div class="card col-md-6 mx-auto">
                    <div class="card-header">
                        <h5> {{ $form_value->Form->title }}

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="view-form-data">
                            <div class="row">
                                @foreach ($array as $keys => $rows)
                                    @foreach ($rows as $row_key => $row)
                                        @if ($row->type == 'checkbox-group')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                    <ul>
                                                        @foreach ($row->values as $key => $options)
                                                            @if (isset($options->selected))
                                                                <li>
                                                                    <label>{{ $options->label }}</label>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'file')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                        @if (property_exists($row, 'value'))
                                                            @if ($row->value)
                                                                @php
                                                                    $allowed_extensions = ['pdf', 'pdfa', 'fdf', 'xdp', 'xfa', 'pdx', 'pdp', 'pdfxml', 'pdxox', 'xlsx', 'csv', 'xlsm', 'xltx', 'xlsb', 'xltm', 'xlw'];
                                                                @endphp
                                                                @if ($row->multiple)
                                                                    <div class="row">
                                                                        @foreach ($row->value as $img)
                                                                            <div class="col-6">
                                                                                @php
                                                                                    $file_name = explode('/', $img);
                                                                                    $file_name = end($file_name);
                                                                                @endphp
                                                                                @if (in_array(pathinfo($img, PATHINFO_EXTENSION), $allowed_extensions))
                                                                                    @php
                                                                                        $file_name = explode('/', $img);
                                                                                        $file_name = end($file_name);
                                                                                    @endphp
                                                                                    <a class="btn btn-info my-2"
                                                                                        href="{{ asset('storage/app/' . $img) }}"
                                                                                        type="image"
                                                                                        download="">{!! substr($file_name, 0, 30) . (strlen($file_name) > 30 ? '...' : '') !!}</a>
                                                                                @else
                                                                                    <img src="{{ Illuminate\Support\Facades\File::exists(Storage::path($img)) ? asset('storage/app/' . $img) : Storage::url('appLogo/78x78.png') }}"
                                                                                        class="img-responsive img-thumbnail mb-2">
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            @if ($row->subtype == 'fineuploader')
                                                                                @if ($row->value[0])
                                                                                    @foreach ($row->value as $img)
                                                                                        @php
                                                                                            $file_name = explode('/', $img);
                                                                                            $file_name = end($file_name);
                                                                                        @endphp
                                                                                        @if (in_array(pathinfo($img, PATHINFO_EXTENSION), $allowed_extensions))
                                                                                            <a class="btn btn-info my-2"
                                                                                                href="{{ asset('storage/app/' . $img) }}"
                                                                                                type="image"
                                                                                                download="">{!! substr($file_name, 0, 30) . (strlen($file_name) > 30 ? '...' : '') !!}</a>
                                                                                        @else
                                                                                            <img src="{{ Illuminate\Support\Facades\File::exists(Storage::path($img))
                                                                                                ? asset('storage/app/' . $img)
                                                                                                : Storage::url('appLogo/78x78.png') }}"
                                                                                                class="img-responsive img-thumbnail mb-2">
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                            @else
                                                                                @if (in_array(pathinfo($row->value, PATHINFO_EXTENSION), $allowed_extensions))
                                                                                    @php
                                                                                        $file_name = explode('/', $row->value);
                                                                                        $file_name = end($file_name);
                                                                                    @endphp
                                                                                    <a class="btn btn-info my-2"
                                                                                        href="{{ asset('storage/app/' . $row->value) }}"
                                                                                        type="image"
                                                                                        download="">{!! substr($file_name, 0, 30) . (strlen($file_name) > 30 ? '...' : '') !!}</a>
                                                                                @else
                                                                                    <img src="{{ Illuminate\Support\Facades\File::exists(Storage::path($row->value)) ? asset('storage/app/' . $row->value) : Storage::url('appLogo/78x78.png') }}"
                                                                                        class="img-responsive img-thumbnailss mb-2">
                                                                                @endif
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'header')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <{{ $row->subtype }}>
                                                        {{ html_entity_decode($row->label) }}
                                                        </{{ $row->subtype }}>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'paragraph')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <{{ $row->subtype }}>
                                                        {{ html_entity_decode($row->label) }}
                                                        </{{ $row->subtype }}>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'radio-group')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                        @foreach ($row->values as $key => $options)
                                                            @if (isset($options->selected))
                                                                {{ $options->label }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'select')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif

                                                    <p>
                                                        @foreach ($row->values as $options)
                                                            @if (isset($options->selected))
                                                                {{ $options->label }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'autocomplete')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                        {{ $row->value }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'number')
                                            <div class="col-12">
                                                <b>{{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                </b>
                                                <p>
                                                    {{ isset($row->value) ? $row->value : '' }}
                                                </p>
                                            </div>
                                        @elseif($row->type == 'text')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    @if ($row->subtype == 'color')
                                                        <div class="p-2" style="background-color: {{ $row->value }};">
                                                        </div>
                                                    @else
                                                        <p>
                                                            {{ isset($row->value) ? $row->value : '' }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($row->type == 'date')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                        {{ isset($row->value) ? date('d-m-Y', strtotime($row->value)) : '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'textarea')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    @if ($row->subtype == 'ckeditor')
                                                        {!! isset($row->value) ? $row->value : '' !!}
                                                    @else
                                                        <p>
                                                            {{ isset($row->value) ? $row->value : '' }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($row->type == 'starRating')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    @php
                                                        $attr = ['class' => 'form-control'];
                                                        if ($row->required) {
                                                            $attr['required'] = 'required';
                                                        }
                                                        $value = isset($row->value) ? $row->value : 0;
                                                        $no_of_star = isset($row->number_of_star) ? $row->number_of_star : 5;
                                                    @endphp
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}
                                                    @if ($row->required)
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                    <div id="{{ $row->name }}" class="starRating"
                                                        data-value="{{ $value }}"
                                                        data-no_of_star="{{ $no_of_star }}">
                                                    </div>
                                                    <input type="hidden" name="{{ $row->name }}"
                                                        value="{{ $value }}">
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'SignaturePad')
                                            @if (property_exists($row, 'value'))
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <img src="{{ asset(Storage::url($row->value)) }}">
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif($row->type == 'break')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <hr class="hr_border">
                                                </div>
                                            </div>
                                        @elseif($row->type == 'location')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {!! Form::label('location_id', 'Location:') !!}
                                                    <iframe width="100%" height="260" frameborder="0" scrolling="no"
                                                        marginheight="0" marginwidth="0"
                                                        src="https://maps.google.com/maps?q={{ $row->value }}&hl=en&z=14&amp;output=embed">
                                                    </iframe>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'video')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}<br>
                                                    <a href="{{ route('selfie.image.download', $form_value->id) }}">
                                                        <button class="btn btn-primary p-2"
                                                            id="downloadButton">{{ __('Download Video') }}</button></a>
                                                </div>
                                            </div>
                                        @elseif($row->type == 'selfie')
                                            <div class="row">
                                                <div class="col-12">
                                                    {{ Form::label($row->name, $row->label, ['class' => 'form-label']) }}<br>
                                                    <img
                                                        src=" {{ Illuminate\Support\Facades\File::exists(Storage::path($row->value)) ? Storage::url($row->value) : Storage::url('appLogo/78x78.png') }}"class="img-responsive img-thumbnailss mb-2">
                                                    <br>
                                                    <a href="{{ route('selfie.image.download', $form_value->id) }}">
                                                        <button class="btn btn-primary p-2"
                                                            id="downloadButton">{{ __('Download Image') }}</button></a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label($row->name, isset($row->label)) }}@if (isset($row->required))
                                                        <span class="text-danger align-items-center">*</span>
                                                    @endif
                                                    <p>
                                                        {{ isset($row->value) ? $row->value : '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="javascript:javascript:history.go(-1)"
                            class="btn btn-secondary float-end">{{ __('Back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <link href="{{ asset('vendor/jqueryform/css/jquery.rateyo.min.css') }}" rel="stylesheet" />
@endpush
@push('script')
    <script src="{{ asset('vendor/jqueryform/js/jquery.rateyo.min.js') }}"></script>
    <script>
        var $starRating = $('.starRating');
        if ($starRating.length) {
            $starRating.each(function() {
                var val = $(this).attr('data-value');
                var no_of_star = $(this).attr('data-no_of_star');
                if (no_of_star == 10) {
                    val = val / 2;
                }
                $(this).rateYo({
                    rating: val,
                    readOnly: true,
                    numStars: no_of_star
                })
            });
        }
    </script>
@endpush
