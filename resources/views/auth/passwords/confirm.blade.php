@extends('layouts.app')
@section('title', __('Confirm Password'))

@section('content')
    <div class="card">
        <div class="mx-auto card-body">
            <div class="">
                <h4 class="mb-3 text-primary">{{ __('Confirm Password') }}</h4>
            </div>
            <div class="text-start">
                {{ Form::open(['route' => ['password.confirm'], 'method' => 'POST','data-validate']) }}

                <div class="mb-3 form-group">
                    {{ Form::label('password', __('Password'), ['class' => 'form-label mb-2']) }}
                    {!! Form::password('password', [
                        'class' => 'form-control',
                        'placeholder' => __('Password'),
                        'required',
                        'id' => 'password',
                        'autocomplete' => 'current-password',
                    ]) !!}
                </div>

                <div class="d-grid">
                    {!! Form::button(__('Confirm Password'), ['type' => 'submit','class' => 'btn btn-primary btn-block mt-2']) !!}
                    @if (Route::has('password.request'))
                        {!! Html::link(route('password.request'),__('Forgot Your Password?'),['class'=>'btn btn-link']) !!}
                    @endif
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
