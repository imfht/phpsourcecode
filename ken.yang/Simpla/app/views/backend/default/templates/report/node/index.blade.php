@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">内容发布统计</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>时段</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>今天</td>
                        <td>{{$node_count['today']}}</td>
                    </tr>
                    <tr>
                        <td>本周</td>
                        <td>{{$node_count['week']}}</td>
                    </tr>
                    <tr>
                        <td>本月</td>
                        <td>{{$node_count['month']}}</td>
                    </tr>
                    <tr>
                        <td>全部</td>
                        <td>{{$node_count['all']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


@stop

