@php
    // $color = \App\Facades\UtilityFacades::getsettings('color');
    $user = \Auth::user();
    $color = $user->theme_color;
    if ($color == 'theme-1') {
        $chatcolor = '#0CAF60';
    } elseif ($color == 'theme-2') {
        $chatcolor = '#584ED2';
    } elseif ($color == 'theme-3') {
        $chatcolor = '#6FD943';
    } elseif ($color == 'theme-4') {
        $chatcolor = '#145388';
    } elseif ($color == 'theme-5') {
        $chatcolor = '#B9406B';
    } elseif ($color == 'theme-6') {
        $chatcolor = '#008ECC';
    } elseif ($color == 'theme-7') {
        $chatcolor = '#922C88';
    } elseif ($color == 'theme-8') {
        $chatcolor = '#C0A145';
    } elseif ($color == 'theme-9') {
        $chatcolor = '#48494B';
    } elseif ($color == 'theme-10') {
        $chatcolor = '#0C7785';
    }

@endphp
@extends('layouts.main')
@section('title', __('Submitted Form'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Submitted Forms of ' . ' ' . $forms_details->title) }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('home'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Submitted Forms of ' . ' ' . $forms_details->title) }} </li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="main-content">
            <section class="section">
                @if (!empty($forms_details->logo))
                    <div class="text-center gallery gallery-md">
                        {!! Form::image(
                            Storage::exists($forms_details->logo)
                                ? asset('storage/app/' . $forms_details->logo)
                                : Storage::url('appLogo/78x78.png'),
                            null,
                            [
                                'class' => 'gallery-item float-none',
                                'id' => 'app-dark-logo',
                            ],
                        ) !!}
                    </div>
                @endif
                <h2 class="text-center">{{ $forms_details->title }}</h2>
                <div class="section-body filter">
                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <div class="card">
                                <div class="card-body">
                                    @can('manage-submitted-form')
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6 responsive-search">
                                                <div class="form-group d-flex justify-content-start">
                                                    {{ Form::text('user', null, ['class' => 'form-control mr-1 ', 'placeholder' => __('Search here'), 'data-kt-ecommerce-category-filter' => 'search']) }}
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 responsive-search">
                                                <div class="form-group row d-flex justify-content-start">
                                                    {{ Form::text('duration', null, ['class' => 'form-control mr-1 created_at', 'placeholder' => __('Select Date Range'), 'id' => 'pc-daterangepicker-1']) }}
                                                    {!! Form::hidden('form_id', $forms_details->id, ['id' => 'form_id']) !!}
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 btn-responsive-search">
                                                {{ Form::button(__('Filter'), ['class' => 'btn btn-primary btn-lg  add_filter button-left']) }}
                                                {{ Form::button(__('Clear Filter'), ['class' => 'btn btn-secondary btn-lg clear_filter']) }}
                                            </div>
                                        </div>
                                    @endcan
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive py-4">
                                                {{ $dataTable->table(['width' => '100%']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12" id="chart_div">
                                    <style>
                                        .pie-chart {
                                            width: 100%;
                                            height: 400px;
                                            margin: 0 auto;
                                            float: right;
                                        }

                                        .text-center {
                                            text-align: center;
                                        }

                                        @media (max-width: 991px) {
                                            .pie-chart {
                                                width: 100%;
                                            }
                                        }
                                    </style>
                                    <div class="row">
                                        @php($key = 1)
                                        @foreach ($chartData as $chart)
                                            <div class="col-md-6 col-xl-4" data-id="1">
                                                <div class="card">
                                                    @if (isset($chart['is_enable_chart']) && $chart['is_enable_chart'] == 'true')
                                                        <div class="card-header">
                                                            <h5 class="mb-0">
                                                                {{ $chart['label'] }}
                                                            </h5>
                                                        </div>
                                                    @endif
                                                    <div class="col-sm-12">
                                                        @if (isset($chart['is_enable_chart']) && $chart['is_enable_chart'] == true && $chart['chart_type'] == 'bar')
                                                            <div id="chartDiv-{{ $key }}"
                                                                class="pie-chart d-flex align-items-center"></div>
                                                        @endif
                                                        @if (isset($chart['is_enable_chart']) && $chart['is_enable_chart'] == true && $chart['chart_type'] == 'pie')
                                                            <div id="chartDive-{{ $key }}"
                                                                class="pie-chart d-flex align-items-center">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                                var colors = '<?php echo $chatcolor; ?>';

                                                function drawChart{{ $key }}() {
                                                    @if (isset($chart['is_enable_chart']) && $chart['is_enable_chart'] == true && $chart['chart_type'] == 'bar')
                                                        var colWidth = (@json(array_keys($chart['options'])).length * 7) + '%';
                                                        var options = {
                                                            chart: {
                                                                type: 'bar',
                                                                toolbar: {
                                                                    show: false
                                                                }
                                                            },
                                                            plotOptions: {
                                                                bar: {
                                                                    columnWidth: colWidth,
                                                                    borderRadius: 5,
                                                                    dataLabels: {
                                                                        position: 'top',
                                                                    },
                                                                }
                                                            },
                                                            colors: colors,
                                                            dataLabels: {
                                                                enabled: false,
                                                            },
                                                            stroke: {
                                                                show: true,
                                                                width: 1,
                                                                colors: ['#fff']
                                                            },
                                                            grid: {
                                                                strokeDashArray: 4,
                                                            },
                                                            series: [{
                                                                name: @json($chart['label']),
                                                                data: @json(array_values($chart['options'])),
                                                            }],
                                                            xaxis: {
                                                                categories: @json(array_keys($chart['options'])),
                                                            },
                                                        };
                                                        var chart = new ApexCharts(document.querySelector("#chartDiv-{{ $key }}"), options);
                                                        chart.render();
                                                    @endif
                                                    @if (isset($chart['is_enable_chart']) && $chart['is_enable_chart'] == true && $chart['chart_type'] == 'pie')
                                                        var options = {
                                                            series: @json(array_values($chart['options'])),
                                                            chart: {
                                                                width: '100%',
                                                                type: 'donut',
                                                            },
                                                            plotOptions: {
                                                                pie: {
                                                                    startAngle: -90,
                                                                    endAngle: 270
                                                                }
                                                            },
                                                            labels: @json(array_keys($chart['options'])),
                                                            dataLabels: {
                                                                enabled: false
                                                            },
                                                            fill: {
                                                                type: 'gradient',
                                                            },
                                                            legend: {
                                                                formatter: function(val, opts) {
                                                                    return val + " - " + opts.w.globals.series[opts
                                                                        .seriesIndex]
                                                                }
                                                            },
                                                            responsive: [{
                                                                breakpoint: 480,
                                                                options: {
                                                                    chart: {
                                                                        width: 200
                                                                    },
                                                                    legend: {
                                                                        position: 'bottom'
                                                                    }
                                                                }
                                                            }]
                                                        };
                                                        var chart = new ApexCharts(document.querySelector("#chartDive-{{ $key }}"), options);
                                                        chart.render();
                                                    @endif
                                                }
                                            </script>
                                            @php($key++)
                                        @endforeach
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" />
    @include('layouts.includes.datatable_css')
@endpush
@push('script')
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('vendor/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendor/apex-chart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/daterangepicker/daterangepicker.min.js') }}"></script>
    @include('layouts.includes.datatable_js')
    {{ $dataTable->scripts() }}
    <script>
        window.onload = function() {
            @php($key = 1)
            @foreach ($chartData as $chart)
                drawChart{{ $key }}();
                @php($key++)
            @endforeach
        };
        document.querySelector("#pc-daterangepicker-1").flatpickr({
            mode: "range"
        });
    </script>
@endpush
