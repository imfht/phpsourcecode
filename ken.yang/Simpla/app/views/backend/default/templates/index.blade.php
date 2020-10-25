@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">控制面板</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

@if(User::find(Auth::user()->id)->roles['rid'] == '3')
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-group fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{$register_count['month']}}</div>
                        <div>本月注册用户</div>
                    </div>
                </div>
            </div>
            <a href="/admin/report/register-count">
                <div class="panel-footer">
                    <span class="pull-left">查看详情</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-edit fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{$node_count['month']}}</div>
                        <div>本月发布内容数量</div>
                    </div>
                </div>
            </div>
            <a href="/admin/report/node-count">
                <div class="panel-footer">
                    <span class="pull-left">查看详情</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-desktop fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">0</div>
                        <div>网站访问统计</div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0)">
                <div class="panel-footer">
                    <span class="pull-left">查看详情</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-github-alt fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">无更新</div>
                        <div>系统是否可更新</div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0)">
                <div class="panel-footer">
                    <span class="pull-left">查看详情</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
@else
<div class="row">
    <br>
    <p class='text-center'>
        <img src="/{{Auth::user()->picture}}" alt="{{Auth::user()->username}}的头像" class="img-rounded author-head" width="50" height="50">
        你好，{{Auth::user()->username}}。
        <br><br>
    </p>
    <p class='text-center'>
        当前时间是：{{date('Y-m-d H:i:s',time())}}
    </p>
    <br>
</div>
@endif
<!-- /.row -->


@stop