@php
    $footer_main_menus = App\Models\FooterSetting::where('parent_id', 0)->get();
    $users = \Auth::user();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title') | {{ Utility::getsettings('app_name') }}</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (Utility::getsettings('seo_setting') == 'on')
        {!! app('seotools')->generate() !!}
    @endif
    <!-- Favicon icon -->
    <link rel="icon"
        href="{{ Utility::getsettings('favicon_logo') ? Storage::url('appLogo/app-favicon-logo.png') : '' }}"
        type="image/png">
    <!-- font css -->

    <link rel="stylesheet" href="{{ asset('vendor/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage-2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/landing-page2/css/landingpage2-responsive.css') }}">
</head>

<body class="theme-6">
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v3">
        <!--header start here-->
        @include('layouts.app_header')
        <!--header end here-->
        @yield('content')
    </div>
    <!-- [ auth-signup ] end -->
    </div>

</body>
<!--scripts start here-->

<!--scripts start here-->
<script src="{{ asset('vendor/landing-page2/js/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/landing-page2/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bouncer.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('vendor/landing-page2/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.min.js') }}"></script>
<!--scripts end here-->
<script>
    var toster_pos = 'right';
    feather.replace();
</script>
<script src="{{ asset('vendor/js/custom.js') }}"></script>
<script>
    @if (session('failed'))
        show_toastr('Failed!', '{{ session('failed') }}', 'danger');
    @endif
    @if (session('errors'))
        show_toastr('Error!', '{{ session('errors') }}', 'danger');
    @endif
    @if (session('successful'))
        show_toastr('SuccessfulLY!', '{{ session('successful') }}', 'success');
    @endif
    @if (session('success'))
        show_toastr('Done!', '{{ session('success') }}', 'success');
    @endif
    @if (session('warning'))
        show_toastr('Warning!', '{{ session('warning') }}', 'warning');
    @endif

    @if (session('status'))
        show_toastr('Great!', '{{ session('status') }}', 'info');
    @endif

    $(document).on('click', '.delete-action', function() {
        var form_id = $(this).attr('data-form-id')
        $.confirm({
            title: '{{ __('Alert !') }}',
            content: '{{ __('Are You sure ?') }}',
            buttons: {
                confirm: function() {
                    $("#" + form_id).submit();
                },
                cancel: function() {}
            }
        });
    });
</script>

<script>
    function myFunction() {
        const element = document.body;
        element.classList.toggle("dark-mode");
        const isDarkMode = element.classList.contains("dark-mode");
        const expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + 30);
        document.cookie = `mode=${isDarkMode ? "dark" : "light"}; expires=${expirationDate.toUTCString()}; path=/`;
        if (isDarkMode) {
            $('.switch-toggle').find('.switch-moon').addClass('d-none');
            $('.switch-toggle').find('.switch-sun').removeClass('d-none');
        } else {
            $('.switch-toggle').find('.switch-sun').addClass('d-none');
            $('.switch-toggle').find('.switch-moon').removeClass('d-none');
        }
    }
    window.addEventListener("DOMContentLoaded", () => {
        const modeCookie = document.cookie.split(";").find(cookie => cookie.includes("mode="));
        if (modeCookie) {
            const mode = modeCookie.split("=")[1];
            if (mode === "dark") {
                $('.switch-toggle').find('.switch-moon').addClass('d-none');
                $('.switch-toggle').find('.switch-sun').removeClass('d-none');
                document.body.classList.add("dark-mode");
            } else {
                $('.switch-toggle').find('.switch-sun').addClass('d-none');
                $('.switch-toggle').find('.switch-moon').removeClass('d-none');
            }
        }
    });
</script>

@stack('script')

@if (Utility::getsettings('cookie_setting_enable') == 'on')
    @include('layouts.cookie_consent')
@endif
</html>
