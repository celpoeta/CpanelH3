@php
    $languages = \App\Facades\UtilityFacades::languages();
    config([
        'captcha.sitekey' => Utility::getsettings('recaptcha_key'),
        'captcha.secret' => Utility::getsettings('recaptcha_secret'),
    ]);
@endphp
@extends('layouts.app')
@section('title', __('Send Mail'))
@section('content')
    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="login-row d-flex">
                <div class="login-col-6">
                    <div class="login-content-inner">
                        <div class="login-title">
                            <h3>{{ __('Email Verify') }}</h3>
                        </div>
                        {{ Form::open(['route' => ['password.email'], 'method' => 'POST', 'data-validate']) }}
                        <div class="mb-3 form-group">
                            {{ Form::label('email', __('Email Address'), ['class' => 'form-label']) }}
                            {!! Form::email('email', null, [
                                'class' => 'form-control',
                                'id' => 'email',
                                'required',
                                'placeholder' => __('Enter email address'),
                                'onfocus',
                            ]) !!}
                        </div>

                        @if (Utility::getsettings('login_recaptcha_status') == '1')
                            <div class="text-center">
                                {!! NoCaptcha::renderJs() !!}
                                {!! NoCaptcha::display() !!}
                            </div>
                        @endif
                        <br>
                        <div class="text-center">
                            {!! Form::button(__('Forgot Password'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                            {!! Html::link(route('login'), __('Back'), ['class' => 'btn btn-secondary text-white']) !!}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="login-media-col">
                    <div class="login-media-inner">
                        <img src="{{ Utility::getsettings('login_image')
                            ? Storage::url(Utility::getsettings('login_image'))
                            : asset('assets/images/auth/img-auth-3.svg') }}"
                            class="img-fluid" />
                        <h3>
                            {{ Utility::getsettings('login_title') ? Utility::getsettings('login_title') : 'Attention is the new currency' }}
                        </h3>
                        <p>
                            {{ Utility::getsettings('login_subtitle') ? Utility::getsettings('login_subtitle') : 'The more effortless the writing looks, the more effort the writer actually put into the process.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
