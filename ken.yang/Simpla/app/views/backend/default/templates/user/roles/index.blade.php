@extends('BackTheme::layout.master')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">角色列表</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-warning" role="alert">
            1、管理员默认拥有所有权限。<br>
            2、匿名用户默认不拥有后台管理权限，即使赋予权限也无法操作。
        </div>
        <a href="/admin/user/roles/add" class="btn btn-default">添加角色</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>角色ID</th>
                    <th>角色名</th>
                    <th>描述</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td><span class="label label-success">{{$role->id}}</span></td>
                    <td>{{$role->title}}</td>
                    <td>{{$role->description}}</td>
                    <td><a href="/admin/user/roles/{{$role->id}}/permission" class="btn btn-link btn-xs">权限设置</a></td>
                    <td><a href="/admin/user/roles/{{$role->id}}/edit" class="btn btn-link btn-xs">编辑</a></td>
                    <td>
                        @if($role->id != '1' && $role->id != '2' && $role->id != '3')
                        <a href="/admin/user/roles/{{$role->id}}/delete" class="btn btn-link btn-xs" data-btnOkLabel="确定" data-btnCancelLabel="取消" data-toggle="confirmation" data-placement="left" data-original-title="确定要删除该角色吗？">删除</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /.row -->
<script>
    $(function () {
        $('[data-toggle="confirmation"]').confirmation({
            onConfirm: function (event, element) {
                var url = element.context.href;
                window.location.href = url;
            }
        });
    });
</script>
@stop