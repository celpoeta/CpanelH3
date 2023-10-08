@extends('layouts.main')
@section('title', 'Create Zoo')
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Create Zoo') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item"><a href="{{ route('blogcategory.index') }}">{{ __('Blog') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create Zoo') }}</li>
        </ul>
    </div>
@endsection
@push('script')
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}"></script>
    <script>
        var vMarker
        var map
        map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 14,
            center: new google.maps.LatLng(19.4326296, -99.1331785),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        vMarker = new google.maps.Marker({
            position: new google.maps.LatLng(19.4326296, -99.1331785),
            draggable: true
        });
        google.maps.event.addListener(vMarker, 'dragend', function(evt) {
            $("#latitud").val(evt.latLng.lat().toFixed(6));
            $("#longitud").val(evt.latLng.lng().toFixed(6));

            map.panTo(evt.latLng);
        });
        map.setCenter(vMarker.position);
        vMarker.setMap(map);

        $("#txtCiudad, #txtEstado, #txtDireccion").change(function() {
            movePin();
        });

        function movePin() {
            var geocoder = new google.maps.Geocoder();
            var textSelectM = $("#txtCiudad").text();
            var textSelectE = $("#txtEstado").val();
            var inputAddress = $("#txtDireccion").val() + ' ' + textSelectM + ' ' + textSelectE;
            geocoder.geocode({
                "address": inputAddress
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    vMarker.setPosition(new google.maps.LatLng(results[0].geometry.location.lat(), results[0]
                        .geometry.location.lng()));
                    map.panTo(new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry
                        .location.lng()));
                    $("#latitud").val(results[0].geometry.location.lat());
                    $("#longitud").val(results[0].geometry.location.lng());
                }

            });
        }
    </script>
@endpush
@section('content')
    <div class="col-sm-8 m-auto">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Create Zoo') }}</h5>
            </div>
            <div class="card-body">
                {!! Form::open([
                    'route' => 'zoos.store',
                    'method' => 'Post',
                    'enctype' => 'multipart/form-data',
                    'data-validate',
                ]) !!}
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('name', 'Nombre científico', ['class' => 'col-form-label']) }}
                            {!! Form::text('common_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter name')]) !!}
                        </div>
                        <div class="form-group  ">
                            {{ Form::label('name', 'Nombre científico', ['class' => 'col-form-label']) }}
                            {!! Form::text('scientific_name', null, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('Enter name'),
                            ]) !!}
                        </div>
                        <div class="form-group  ">
                            {{ Form::label('name', 'Descripción', ['class' => 'col-form-label']) }}
                            {!! Form::textarea('description', null, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('Enter name'),
                            ]) !!}
                        </div>
                        <div class="form-group  ">
                            {{ Form::label('name', 'Destribucion geográfica', ['class' => 'col-form-label']) }}
                            {!! Form::text('distribution', null, ['class' => 'form-control', 'required', 'placeholder' => __('Description')]) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group  row">
                            <div class="col-sm-8">
                                {{ Form::label('category_id', __('category'), ['class' => 'col-form-label']) }}
                                {!! Form::select('category_id', $categories, null, ['class' => 'form-select', 'id' => 'category_id']) !!}
                            </div>
                            <div class="col-sm-4">
                                {{ Form::label('name', 'En extinción', ['class' => 'col-form-label']) }}
                                <div class="col-md-2 form-check form-switch custom-switch-v1">
                                    <label class="custom-switch mt-2 float-right">
                                        <input name="risk" data-onstyle="primary" class="form-check-input input-primary"
                                            type="checkbox" />
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name',  __('Nota de extinción'), ['class' => 'col-form-label']) }}
                            {!! Form::textarea('risk_description', null, [
                                'class' => 'form-control',
                                'required',
                                'placeholder' => __('Description'),
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('images', __('Images'), ['class' => 'form-label']) }} *
                            {!! Form::file('url_image', ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Habitat', ['class' => 'col-form-label']) }}
                            {!! Form::text('habitat', null, ['class' => 'form-control', 'required', 'placeholder' => __('Description')]) !!}
                        </div>
                    </div>
                    <div class="form-group" id="map_canvas" style="height:350px">
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <input type="text" id="latitud" name="latitud" class="form-control" placeholder="latitude">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" id="longitud" name="longitud" class="form-control" placeholder="longitud">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="float-end">
                    <a href="{{ route('zoos.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    </div>
@endsection
