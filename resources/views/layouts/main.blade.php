<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ \App\Facades\UtilityFacades::getsettings('rtl') == '1' ? 'rtl' : '' }}">

<head>
    @php
        $user = \Auth::user();
        $primary_color = $user->theme_color;
        if (isset($primary_color) && $primary_color != '') {
            $color = $primary_color;
        } else {
            $color = 'theme-2';
        }
    @endphp
    <title>@yield('title') | {{ Utility::getsettings('app_name') }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (Utility::getsettings('seo_setting') == 'on')
        {!! app('seotools')->generate() !!}
    @endif
    <!-- Favicon icon -->
    <link rel="icon"
        href="{{ Utility::getsettings('favicon_logo') ? Storage::url('appLogo/app-favicon-logo.png') : '' }}"
        type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/notifier.css') }}">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/jquery-confirm.min.css') }}">
    <!-- Bootstrap datetimepicker css -->
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">


    @if ($user->rtl_layout == 1)
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
    @endif
    @if ($user->dark_layout == 1)
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @elseif ($user->rtl_layout == 0)
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/css/custom.css') }}">
    @stack('style')
</head>

<body class="{{ $color }}">

    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Mobile header ] End -->
    <!-- [ navigation menu ] start -->
    @include('layouts.sidebar')

    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    @include('layouts.header')

    <!-- Modal -->
    <div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <h6 class="mt-2">
                        <i data-feather="monitor" class="me-2"></i>{{ __('Desktop settings') }}
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting1" checked />
                        <label class="pl-1 form-check-label f-w-600"
                            for="pcsetting1">{{ __('Allow desktop notification') }}</label>
                    </div>
                    <p class="text-muted ms-5">
                        {{ __('you get lettest content at a time when data will updated') }}
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting2" />
                        <label class="pl-1 form-check-label f-w-600" for="pcsetting2">{{ __('Store Cookie') }}</label>
                    </div>
                    <h6 class="mt-5 mb-0">
                        <i data-feather="save" class="me-2"></i>{{ __('Application settings') }}
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting3" />
                        <label class="pl-1 form-check-label f-w-600"
                            for="pcsetting3">{{ __('Backup Storage') }}</label>
                    </div>
                    <p class="mb-4 text-muted ms-5">
                        {{ __('Automaticaly take backup as par schedule') }}
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting4" />
                        <label class="pl-1 form-check-label f-w-600"
                            for="pcsetting4">{{ __('Allow guest to print file') }}</label>
                    </div>
                    <h6 class="mt-5 mb-0">
                        <i data-feather="cpu" class="me-2"></i>{{ __('System settings') }}
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting5" checked />
                        <label class="pl-1 form-check-label f-w-600"
                            for="pcsetting5">{{ __('View other user chat') }}</label>
                    </div>
                    <p class="text-muted ms-5">{{ __('Allow to show public user message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Header ] end -->
</body>

<!-- [ Main Content ] start -->
<div class="dash-container">
    <div class="dash-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    @yield('breadcrumb')
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        @yield('content')

        <!-- [ Main Content ] end -->
    </div>
</div>
<!-- [ Main Content ] end -->
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <span class="text-muted">&copy; {{ date('Y') }} {{ Utility::getsettings('app_name') }}</span>
        </div>
        <div class="py-1">
        </div>
    </div>
</footer>

<div class="modal fade modal-animate anim-blur" role="dialog" id="common_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="body">
                <p></p>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-animate anim-blur" role="dialog" id="common_modal1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-animate anim-blur" role="dialog" id="common_modal2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
        </div>
    </div>
</div>
<div class="top-0 p-3 position-fixed end-0" style="z-index: 99999">
    <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"> </div>
            <button type="button" class="m-auto btn-close btn-close-white me-2" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
<script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/dash.js') }}"></script>
<script src="{{ asset('vendor/modules/tooltip.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifier.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
<script>
    var toster_pos = 'right';
</script>
<script src="{{ asset('vendor/js/custom.js') }}"></script>

@if (!empty(setting('gtag')))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('gtag') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ setting('gtag') }}');
    </script>
@endif
<script>
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
    $(document).on("click", "#kt_activities_toggle", function() {
        $.ajax({
            url: '{{ route('read.notification') }}',
            data: {
                _token: $("meta[name='csrf-token']").attr('content')
            },
            method: 'post',
        }).done(function(data) {
            if (data.is_success) {
                $("#kt_activities_toggle").find(".animation-blink").remove();
            }
        });
    });
    $(document).ready(function() {
        $('.add_dark_mode').on('click', function() {
            var $this = $(this);
            $.ajax({
                url: "{{ route('change.theme.mode') }}",
                method: "POST",
                data: {
                    _token: $("meta[name='csrf-token']").attr('content'),
                },
                success: function(response) {
                    if (response.mode == 1) {
                        $this.find('i').removeClass('ti-sun').addClass('ti-moon');
                        $(".m-header > .b-brand > img").attr(
                            "src",
                            "{{ Storage::url(setting('app_logo')) ? Storage::url('appLogo/app-logo.png') : Storage::url('78x78.png') }}"
                        );
                        document.querySelector("#main-style-link").setAttribute("href",
                            "{{ asset('assets/css/style-dark.css') }}"
                        );
                    } else {
                        $this.find('i').removeClass('ti-moon').addClass('ti-sun');
                        document.querySelector(".m-header > .b-brand > img").setAttribute(
                            "src",
                            "{{ Storage::url(setting('app_dark_logo')) ? Storage::url('appLogo/app-dark-logo.png') : Storage::url('78x78.png') }}"
                        );
                        $(".m-header > .b-brand > img").attr(
                            "src",
                            response.app_dark_logo_url
                        );
                        document.querySelector("#main-style-link").setAttribute("href",
                            "{{ asset('assets/css/style.css') }}"
                        );
                    }
                }
            });
        });
    });
</script>
@include('layouts.includes.alerts')
@stack('script')
@if (Utility::getsettings('cookie_setting_enable') == 'on')
    @include('layouts.cookie_consent')
@endif
</body>
</html>
