@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>菜单列表</legend>
        <div class="table-top-button-box">
            <a href="{{ route('menus.create') }}" class="layui-btn layui-btn-small">
                <i class="layui-icon">&#xe608;</i> 添加菜单
            </a>
        </div>
        <div class="layui-field-box">
            <table class="layui-table">
                <colgroup>
                    <col width="100">
                    <col width="250">
                    <col width="300">
                    <col width="200">
                    <col width="55">
                    <col width="200">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>菜单名称</th>
                    <th>路由</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                        <tr>
                            <td>{{ $menu->id }}</td>
                            <td>{{ '|' . str_repeat(' - - ', $menu->lev).$menu->display_name }}</td>
                            <td><code>{{ $menu->uri }}</code></td>
                            <td>
                                <a href=" {{ route('menus.edit', ['id'=>$menu->id]) }}" class="layui-btn layui-btn-primary layui-btn-mini">编辑</a>
                                <form action="{{ route('menus.destroy', ['id'=>$menu->id]) }}" class="form-list-btn" method="POST">
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