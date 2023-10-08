@extends('layouts.form')
@section('title', __('Response'))
@section('content')
    <div class="section-body">
        <div class="row mx-0">
            <div class="col-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center w-100">{{ $form->title }}</h4>
                    </div>
                    <div class="card-body">
                        @if (!empty($form->logo))
                            <div class="text-center">
                                {!! Form::image(
                                    Storage::exists($form->logo) ? asset('storage/app/' . $form->logo) : Storage::url('appLogo/78x78.png'),
                                    null,
                                    [
                                        'id' => 'app-dark-logo',
                                        'class' => 'img img-responsive my-5 w-30 justify-content-center text-center',
                                    ],
                                ) !!}
                            </div>
                        @endif
                        <h2 class="text-center w-100">{{ $form->success_msg }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
