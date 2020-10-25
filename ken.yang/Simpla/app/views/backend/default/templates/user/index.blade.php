@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">用户管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <!--筛选-->
        {{ Form::open(array('method' => 'post')) }}
        <div class="form-group input-group form-filter-150">
            <span class="input-group-addon">用户ID</span>
            <input type="number" name="uid" class="form-control" value="{{$choose['uid']}}">
        </div>
        <div class="form-group input-group form-filter">
            <span class="input-group-addon">用户名</span>
            <input type="text" name="username" class="form-control" value="{{$choose['username']}}">
        </div>
        <div class="form-group input-group form-filter">
            <span class="input-group-addon">邮箱</span>
            <input type="email" name="email" class="form-control" value="{{$choose['email']}}">
        </div>
        <button class="btn btn-default" type="submit">筛选</button>
        <a class="btn btn-default" href="{{Request::url()}}">重置</a>
        {{Form::close()}}

        <!--用户列表-->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>用户ID</th>
                        <th>用户名</th>
                        <th>邮箱</th>
                        <th>当前状态</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>编辑</th>
                        <th>删除</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->username}}</td>
                        <td>{{$user->email}}</td>
                        <td>
                            @if($user->status)
                                <span class="label label-success">活跃</span>
                            @else
                                <span class="label label-danger">锁定</span>
                            @endif
                        </td>
                        <td>{{$user->created_at}}</td>
                        <td>{{$user->updated_at}}</td>
                        <td>
                            <a href="/admin/user/{{$user->id}}/edit" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                        <td>
                            <a href="/admin/user/{{$user->id}}/delete" name="{{$user->id}}"  class="btn btn-danger btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该用户吗？"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$users->links()}}
        </div>

    </div>
</div>
<!-- /.row -->
<script>
    $(function () {
        $('[data-toggle="confirmation"]').confirmation({
            onConfirm: function (event, element) {
                var url = element.context.pathname;
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 'error') {
                            alert(data.message);
                        } else {
                            location.reload();
                        }
                    }
                });
            }
        });
    });
</script>
@stop