@php
    $phone = Auth()->user()->phone;
    $email = Auth()->user()->email;
@endphp
@extends('layouts.app')
@section('title', __('SMS'))
@section('content')
    <div class="card-body">
        <div class="">
            <h3 class="mb-3 f-w-600">{{ __('SMS notice') }}</h3>
        </div>
        <div class="">
            <small class="text-muted">{{ __('Enter the pin from SMS') }}</small><br/>
            {!! Form::open([
                'route' => 'sms.noticeverification',
                'data-validate',
                'method' => 'POST',
                'class' => 'form-horizontal',
            ]) !!}

            <div class="form-group mb-3">
                {{ Form::label('phone', __('Phone Number'), ['class' => 'form-label']) }}
                {!! Form::text('phone', $phone, [
                    'autofocus' => '',
                    'readonly',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Enter phone Number',
                    'class' => 'form-control',
                ]) !!}
            </div>
            <input type="hidden" name="email" value="{{ isset($email) ? $email : $_GET['email'] }}">

            <div class="d-grid">
                <button class="btn btn-primary btn-block mt-2" type="submit">{{ __('Send Code') }}</button>
            </div>
            {!! Form::close() !!}

            <p class="my-4 text-center">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="f-w-400">Logout</a>
            </p>
            {!! Form::open([
                'route' => 'logout',
                'method' => 'POST',
                'id' => 'logout-form',
                'class' => 'd-none',
            ]) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
