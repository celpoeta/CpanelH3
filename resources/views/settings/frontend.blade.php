@php
    use App\Facades\UtilityFacades;
    $lang = \App\Facades\UtilityFacades::getValByName('default_language');
    $primary_color = \App\Facades\UtilityFacades::getsettings('color');
    if (isset($primary_color)) {
        $color = $primary_color;
    } else {
        $color = 'theme-4';
    }
    $roles = App\Models\Role::whereNotIn('name', ['Super Admin', 'Admin'])
        ->pluck('name', 'name')
        ->all();
@endphp

@extends('layouts.main')
@section('title', __('Frontend Page'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Frontend Page') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), ['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Frontend Page') }}</li>
        </ul>
    </div>
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
              <div class="col-xl-3">
                    <div class="card sticky-top">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#app_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('App Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#menu_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Menu Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#features_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Feature Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#faqs_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Faq Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#testimonial_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Testimonial Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#side_feature_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Side Feature Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#privacy_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Privacy Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#contactus_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Contact Us Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#term_condition_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Term & Condition Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#login_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('LogIn Page Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#recaptcha_setting"
                                class="border-0 list-group-item list-group-item-action">{{ __('Recaptcha Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

@endsection
