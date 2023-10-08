@extends('layouts.app')
@section('title', __('2FA'))
@section('content')
    <div class="card">
        <div class="mx-auto card-body">
            <div class="">
                <h4 class="mb-3 text-primary">{{ __('Two Factor Authentication') }}</h4>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="text-start">
                {!! Form::open([
                    'route' => ['2fa'],
                    'method' => 'POST',
                    'data-validate',
                    'class' => 'form-horizontal',
                ]) !!}
                <div class="mb-3 form-group">
                    {{ Form::label('one_time_password', __('One time Password'), ['class' => 'form-label mb-2']) }}
                    {{ Form::text('one_time_password', old('one_time_password'), ['class' => 'form-control', 'onfocus', 'required', 'id' => 'one_time_password']) }}
                    @if ($errors->has('email'))
                        <span class="invalid-feedback d-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    @if ($errors->has('one_time_password'))
                        <span class="invalid-feedback d-block">
                            <strong>{{ $errors->first('one_time_password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="text-center">
                    {!! Form::button(__('Verify'), ['type' => 'submit','class' => 'form-control btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
