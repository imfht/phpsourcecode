@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">操作</h3>
                </div>
                <div class="box-body">
                    <a href="{{route('role.create')}}" class="btn btn-primary">新建角色</a>
                </div>
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">角色列表</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody><tr>
                            <th style="width: 10px">#</th>
                            <th>角色名称</th>
                            <th>显示名称</th>
                            <th>描述</th>
                            <th>操作</th>
                        </tr>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{$role->id}}</td>
                            <td>{{$role->name}}</td>
                            <td>{{$role->display_name}}</td>
                            <td>{{$role->description}}</td>
                            <td><a class="btn btn-xs" href="{{url('admin/role/'.$role->id.'/remove')}}">删除</a><a class="btn btn-xs" href="{{url('admin/role/'.$role->id.'/edit')}}">修改</a>
                                <a class="btn btn-xs" href="{{url('admin/role/permission/'.$role->id)}}">添加模块</a></td>
                        </tr>
                        @endforeach
                        </tbody></table>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{$roles->render()}}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection