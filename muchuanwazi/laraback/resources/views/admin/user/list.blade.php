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
                    <a href="{{route('user.create')}}" class="btn btn-primary">新建用户</a>
                </div>
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">用户列表</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>用户名</th>
                            <th>电子邮件</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->status==1?'启用':'停用'}}</td>
                                <td>{{$user->created_at}}</td>
                                <td>{{$user->updated_at}}</td>
                                <td>
                                    <a onclick="event.preventDefault();document.getElementById('delete-form').submit();"
                                       href="javascript:;" class="btn btn-xs">{{$user->status==1?'停用':'启用'}}</a>
                                    {!! Form::open(['route' => ['user.destroy','id'=>$user->id],'id'=>'delete-form','method' => 'delete','style'=>'display:none']) !!}
                                    {!! Form::close() !!}
                                    <a class="btn btn-xs" href="{{url('admin/user/'.$user->id.'/edit')}}">修改</a>
                                    <a class="btn btn-xs" href="{{url('admin/user/role/'.$user->id)}}">添加角色</a>
                                    <a class="btn btn-xs" href="{{url('admin/user/'.$user->id.'/avatar')}}">添加照片</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{$users->render()}}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection