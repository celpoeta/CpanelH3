@extends('layouts.app')
@section('title', __('Verify Email'))
@section('content')
    <div class="card">
        <div class="mx-auto card-body">
            <div class="">
                <h4 class="mb-3 text-primary">{{ __('Verify Your Email Address') }}</h4>
            </div>
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif
            <div class="text-start">
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                {{ Form::open(['route' => ['verification.resend'], 'method' => 'POST', 'class' => 'd-inline']) }}
                {!! Form::button(__('click here to request another'), ['type' => 'submit','class' => 'btn btn-link p-0 m-0 align-baseline']) !!}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
