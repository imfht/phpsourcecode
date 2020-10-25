@extends('layouts.admin')
@section('content')
    <fieldset class="layui-elem-field">
        <legend>添加角色</legend>
        <form class="layui-form" action="{{ route('roles.store') }}" method="POST" style="margin-top: 10px;">
            {!! csrf_field() !!}
            <div class="layui-form-item" style="margin-bottom: 20px;">
                <label class="layui-form-label">角色名称: </label>
                <input type="text" name="name"   placeholder="请输入角色标识" class="layui-input" style="width:20%; display: inline-block;">
                <input type="text" name="display_name"   placeholder="请输入角色名称" class="layui-input" style="width:20%; display: inline-block;">
                <button class="layui-btn layui-btn-small" lay-submit lay-filter="go" style="margin-bottom: 0px">保存</button>
            </div>
        </form>
    </fieldset>
    <fieldset class="layui-elem-field layui-field-title">
        <legend>角色列表</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <colgroup>
                    <col width="10">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>角色名称</th>
                    <th>角色标识</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td><code>{{ $role->name }}</code></td>
                        <td>
                            <a href=" {{ route('roles.permissions', ['id'=>$role->id]) }}" class="layui-btn layui-btn-primary layui-btn-mini">分配权限</a>
                            <a href=" {{ route('roles.users', ['id'=>$role->id]) }}" class="layui-btn layui-btn-primary layui-btn-mini">组成员</a>
                            <form action="{{ route('roles.destroy', ['id'=>$role->id]) }}" method="POST" class="form-list-btn" >
                                {!! csrf_field() !!}
                                {!! method_field('delete') !!}
                                <button class="layui-btn layui-btn-primary layui-btn-mini">删除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </fieldset>

@endsection