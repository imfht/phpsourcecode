@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/datatables.min.css'>
<link rel='stylesheet' href='/css/stats.css'>
<link rel='stylesheet' href='/css/jquery-jvectormap.css'>
<link rel='stylesheet' href='/css/bootstrap-datetimepicker.min.css'>
@endsection

@section('content')
<div ng-controller="StatsCtrl" class="ng-root">
    <div class="stats-header bottom-padding">
        <h3>状态</h3>
        <div class="row">
            <div class="col-md-3 col-md-offset-3 link-meta">
                <p>
                    <b>短链接: </b>
                    <a target="_blank" href="{{ env('APP_PROTOCOL') }}/{{ env('APP_ADDRESS') }}/{{ $link->short_url }}">
                        {{ env('APP_ADDRESS') }}/{{ $link->short_url }}
                    </a>
                </p>
                <p>
                    <b>原地址: </b>
                    <a target="_blank" href="{{ $link->long_url }}">{{ str_limit($link->long_url, 50) }}</a>
                </p>
                {{-- <p>
                    <em>提示：清除正确的日期界限（底部框）以将其设置为当前日期和时间。 除非将正确的日期界限设置为当前时间，否则不会显示新的点击。</em>
                </p> --}}
            </div>
            <div class="col-md-3">
                <form action="" method="GET">
                    <div class="form-group">
                        <div class='input-group date' id='left-bound-picker'>
                            <input type="text" class="form-control" name="left_bound">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class='input-group date' id='right-bound-picker'>
                            <input type="text" class="form-control" name="right_bound">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <input type="submit" value="Refresh" class="form-control">
                </form>
            </div>
        </div>
    </div>

    <div class="row bottom-padding">
        <div class="col-md-8">
            <h4>Traffic over Time</h4> (total: {{ $link->clicks }})
            <canvas id="dayChart"></canvas>
        </div>
        <div class="col-md-4">
            <h4>Traffic sources</h4>
            <canvas id="refererChart"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>Map</h4>
            <div id="mapChart"></div>
        </div>
        <div class="col-md-6">
            <h4>Referers</h4>
            <table class="table table-hover" id="refererTable">
                <thead>
                    <tr>
                        <th>Host</th>
                        <th>Clicks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($referer_stats as $referer)
                        <tr>
                            <td>{{ $referer->label }}</td>
                            <td>{{ $referer->clicks }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection

@section('js')
{{-- Load data --}}
<script>
// Load data
var dayData = JSON.parse('{!! json_encode($day_stats) !!}');
var refererData = JSON.parse('{!! json_encode($referer_stats) !!}');
var countryData = JSON.parse('{!! json_encode($country_stats) !!}');

// Load datepicker dates
var datePickerLeftBound = '{{ $left_bound }}';
var datePickerRightBound = '{{ $right_bound }}';
</script>

{{-- Include extra JS --}}
<script src='/js/lodash.min.js'></script>
<script src='/js/chart.bundle.min.js'></script>
<script src='/js/datatables.min.js'></script>
<script src='/js/jquery-jvectormap.min.js'></script>
<script src='/js/jquery-jvectormap-world-mill.js'></script>
<script src='/js/moment.min.js'></script>
<script src='/js/bootstrap-datetimepicker.min.js'></script>
<script src='/js/StatsCtrl.js'></script>
@endsection
