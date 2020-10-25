<!--403没有权限访问-->
@extends('Theme::layout.page-single')


@section('content')


<div class="col-md-4 col-md-offset-4">
    <div class="login-panel panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">没有权限</h3>
        </div>
        <div class="panel-body">
            <div class="alert alert-danger" role="alert">对不起，你没有权限访问！</div>
        </div>
    </div>
</div>


@stop