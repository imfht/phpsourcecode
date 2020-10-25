@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">菜单管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{$data['menu_type']['title']}}
                <a href="/admin/menu/add/{{$data['menu_type']['id']}}" class="btn btn-default btn-xs">添加</a>
            </div>
            <div class="panel-body">
                <p>1、修改显示位置直接编辑下面的数字，数字越大越靠后排列</p>
            </div>
            
            {{Form::open(array('url'=>'admin/menu/weight/'.$data['tid'].'/edit','method' => 'post'))}}
            <table class="table">
                <thead>
                    <tr>
                        <th>菜单名</th>
                        <th>排序位置</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    {{Base::outputTrTree($data['menus'],$data['menu_type'],'menu')}}
                </tbody>
            </table>
            <div class="btn-current">
                <button class="btn btn-default" type="submit">保存</button>
            </div>
            {{Form::close()}}

        </div>
    </div>
</div>
<!-- /.row -->

@stop