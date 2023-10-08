<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ \App\Facades\UtilityFacades::getsettings('rtl') == '1' ? 'rtl' : '' }}">

<head>
    @php
        $primary_color = \App\Facades\UtilityFacades::getsettings('color');
        if (isset($primary_color)) {
            $color = $primary_color;
        } else {
            $color = 'theme-4';
        }
    @endphp
    <title>@yield('title') | {{ Utility::getsettings('app_name') }}</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('vendor/js/plugins/croppie/css/croppie.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link"> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">

    @if (Utility::getsettings('rtl') == '1')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
    @endif

    @if (Utility::getsettings('dark_mode') == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @elseif(Utility::getsettings('rtl') != '1')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @endif
    <link rel="stylesheet" href="{{ asset('vendor/css/custom.css') }}">
    @stack('style')
</head>

<body class="{{ $color }}">
    <div class="loading">Loading…</div>
    <div class="dash-content">
        @yield('content')
    </div>
    <div class="modal fade" role="dialog" id="common_modal">
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
    <div class="modal fade" role="dialog" id="common_modal1">
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
</body>

<script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('vendor/modules/tooltip.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifier.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>

<script>
    var toster_pos = 'right';
    window.addEventListener("load", function() {
        // Hide the loader when the page is fully loaded
        var loader = document.querySelector(".loading");
        $(loader).addClass('d-none');
    });
</script>
<script src="{{ asset('vendor/js/custom.js') }}"></script>
<script>
    feather.replace();
</script>

<script>
    feather.replace();
    var multipleCancelButton = new Choices('#sschoices-multiple-remove-button', {
        removeItemButton: true,
    });
</script>

@include('layouts.includes.alerts')
@stack('script')
</body>

</html>
