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
                            <th>模块添加数目</th>
                            <td>{{$rolePermissionCount}}</td>
                        </tr>


                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">

                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->



    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">为角色添加可管理的模块</h3>
                </div>
                {!! Form::open(['route' => 'role.permission.store']) !!}
                <input type="hidden" name="roleid" value="{{$role->id}}" />
                <div class="box-body">

                        {{Form::bsCheckbox($rolePermission)}}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ Form::bsButton('cancel','btn-default','取消',route('role.index')) }}
                    {{ Form::bsButton('submit','btn-info pull-right','保存') }}
                </div><!-- /.box-footer-->
                {!! Form::close() !!}
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection