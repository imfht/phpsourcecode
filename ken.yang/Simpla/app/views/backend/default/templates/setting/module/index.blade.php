@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">模块管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-warning" role="alert">
            1、首次开启模块，如果存在数据库安装，会一并进行安装<br>
            2、若要删除模块代码，请先删除模块，再删除模块代码
        </div>
        @foreach($module_list as $key=>$row)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{$key}}
            </div>
            <div class="panel-body">
                @if(count($row))
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>模块名</th>
                            <th>描述</th>
                            <th>版本</th>
                            <th>状态</th>
                            <th>操作</th>
                            <th>安装/删除</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($row as $module)
                        <tr>
                            <td>{{$module['name']}}</td>
                            <td>{{$module['description']}}</td>
                            <td>{{$module['version']}}</td>
                            <td>
                                @if($module['enabled'])
                                <span class="label label-success">开启</span>
                                @else
                                <span class="label label-danger">关闭</span>
                                @endif
                                @if($module['install'])
                                <span class="label label-success">已安装</span>
                                @endif
                            </td>
                            <td>
                                @if($module['enabled'])
                                <a href="/admin/setting/module/{{$module['machine_name']}}/close">关闭</a>
                                @else
                                <a href="/admin/setting/module/{{$module['machine_name']}}/open">开启</a>
                                @endif
                            </td>
                            <td>
                                @if($module['install_file'])
                                @if($module['install'])
                                <a href="/admin/setting/module/{{$module['machine_name']}}/uninstall">删除</a>
                                @else
                                <a href="/admin/setting/module/{{$module['machine_name']}}/install">安装</a>
                                @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <!-- /.row -->
</div>
@stop