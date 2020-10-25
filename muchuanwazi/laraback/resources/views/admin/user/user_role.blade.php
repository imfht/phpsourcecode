@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">用户信息</h3>
                </div>

                <div class="box-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$user->id}}</td>
                        </tr>
                        <tr>
                            <th>用户名称</th>
                            <td>{{$user->name}}</td>
                        </tr>
                        <tr>
                            <th>电子邮件</th>
                            <td>{{$user->email}}</td>
                        </tr>
                        <tr>
                            <th>创建时间</th>
                            <td>{{$user->created_at}}</td>
                        </tr>
                        <tr>
                            <th>更新时间</th>
                            <td>{{$user->updated_at}}</td>
                        </tr>
                        <tr>
                            <th>角色添加数目</th>
                            <td>{{$userRoleCount}}</td>
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
                    <h3 class="box-title">为用户添加角色</h3>
                </div>
                {!! Form::open(['route' => 'user.role.store']) !!}
                <input type="hidden" name="userid" value="{{$user->id}}" />
                <div class="box-body">

                        {{Form::bsCheckbox($userRole)}}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ Form::bsButton('cancel','btn-default','取消',route('user.index')) }}
                    {{ Form::bsButton('submit','btn-info pull-right','保存') }}
                </div><!-- /.box-footer-->
                {!! Form::close() !!}
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection