@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>权限列表</legend>
        <div class="table-top-button-box">
            <a href="{{ route('permissions.create') }}" class="layui-btn layui-btn-small">
                <i class="layui-icon">&#xe608;</i> 添加权限
            </a>
        </div>
        <div class="layui-field-box">
            <table class="layui-table">
                <colgroup>
                    <col width="10">
                    {{--<col width="250">--}}
                    {{--<col width="300">--}}
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>权限名称</th>
                    <th>权限标识</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>{{ str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $permission->lev).'|  -  -  '.$permission->display_name }}</td>
                        <td><code>{{ $permission->name }}</code></td>
                        <td>
                            <a href=" {{ route('permissions.edit', ['id'=>$permission->id]) }}" class="layui-btn layui-btn-primary layui-btn-mini">编辑</a>
                            <form action="{{ route('permissions.destroy', ['id'=>$permission->id]) }}" class="form-list-btn" method="POST">
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