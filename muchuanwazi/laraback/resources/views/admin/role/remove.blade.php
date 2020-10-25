@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">角色信息</h3>
                </div>

                <div class="box-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$role->id}}</td>
                        </tr>
                        <tr>
                            <th>角色名称</th>
                            <td>{{$role->name}}</td>
                        </tr>
                        <tr>
                            <th>显示名称</th>
                            <td>{{$role->display_name}}</td>
                        </tr>
                        <tr>
                            <th>角色描述</th>
                            <td>{{$role->description}}</td>
                        </tr>
                        <tr>
                            <th>关联模块数</th>
                            <td>{{$rolePermissionCount}}</td>
                        </tr>
                        <tr>
                            <th>关联用户数</th>
                            <td>{{$roleUserCount}}</td>
                        </tr>

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                   <a href="{{route('role.index')}}" class="btn btn-default">回到列表</a>
                    <a onclick="event.preventDefault();if(confirm('确认删除？\r\n如果删除，将会同时将加入到该角色的用户移出该角色。')){document.getElementById('delete-form').submit();}" href="javascript:;" class="btn btn-info pull-right btn-flat">删除</a>
                    {!! Form::open(['route' => ['role.destroy','id'=>$role->id],'id'=>'delete-form','method' => 'delete']) !!}
                    {!! Form::close() !!}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection