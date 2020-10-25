@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">主题管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            @foreach($theme_list as $row)
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <img data-src="{{$row['screenshot']}}" alt="" src="{{$row['screenshot']}}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                    <div class="caption">
                        <h3>{{$row['name']}}<small><span class="label label-success pull-right">{{$row['version']}}</span></small></h3>
                        <p>{{$row['description']}}</p>
                            @if($theme_default == $row['machine_name'])
                        <div class="btn btn-primary">当前默认主题</div>
                        @else
                        <a href="/admin/setting/theme/{{$row['machine_name']}}" class="btn btn-default">设为默认主题</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <!-- /.row -->
</div>
@stop