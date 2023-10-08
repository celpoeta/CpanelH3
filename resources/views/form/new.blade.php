@extends('layouts.main')
@section('title', __('Form Fill'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Form Fill') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('forms.index'), __('Forms'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Form Fill') }} </li>
        </ul>
    </div>
@endsection
@section('content')
<div class="row">
    <div class="main-content">
        <section class="section">
            <div class="section-body filter">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="card p-3">
                            <div class="card-body">
                                <div class="container">
                                    <form method="POST" action="{{ route('webcam.capture') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="my_camera"></div>
                                                <br/>
                                                <input type=button value="Take Snapshot" onClick="take_snapshot()">
                                                <input type="hidden" name="image" class="image-tag">
                                            </div>
                                            <div class="col-md-6">
                                                <div id="results">Your captured image will appear here...</div>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <br/>
                                                <button class="btn btn-success">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<style type="text/css">
    #results { padding:20px; border:1px solid; background:#ccc; }
</style>
@endpush
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

   <script language="JavaScript">
    Webcam.set({
        width: 490,
        height: 350,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach( '#my_camera' );

    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }
</script>
@endpush
