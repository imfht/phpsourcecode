@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>管理员列表</legend>
        <div class="table-top-button-box">
            <a href="{{ route('admins.create') }}" class="layui-btn layui-btn-small">
                <i class="layui-icon">&#xe608;</i> 添加管理员
            </a>
        </div>
        <div class="layui-field-box">
            <table class="layui-table">
                <colgroup>
                    <col width="100">
                    <col width="250">
                    <col width="300">
                    <col width="200">
                    <col width="150">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>管理员昵称</th>
                    <th>登录账号</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>{{ $admin->nickname }}</td>
                        <td><code>{{ $admin->email }}</code></td>
                        <td>{{ $admin->active ? '正常' : '禁用' }}</td>
                        <td>
                            @if($admin->active == 1)
                                <a href="javascript:;" data-route="{{ route('admins.active', ['id'=>$admin->id]) }}" data-active="disable" class="layui-btn layui-btn-danger layui-btn-mini btn-active">禁用</a>
                            @else
                                <a href="javascript:;" data-route="{{ route('admins.active', ['id'=>$admin->id]) }}" data-active="enable" class="layui-btn layui-btn-normal layui-btn-mini btn-active">启用</a>
                            @endif


                            <a href=" {{ route('admins.edit', ['id'=>$admin->id]) }}" class="layui-btn layui-btn-primary layui-btn-mini">编辑</a>

                            <form action="{{ route('admins.destroy', ['id'=>$admin->id]) }}" class="form-list-btn" method="POST">
                                {!! csrf_field() !!}
                                {!! method_field('delete') !!}
                                <button class="layui-btn layui-btn-primary layui-btn-mini">删除</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="page">
                {{ $admins->links() }}
            </div>
        </div>
    </fieldset>

@endsection

@section('script')
<script>
    layui.use(['jquery', 'ajax'], function () {
        var $ = layui.jquery, ajax = layui.ajax()
        $('.btn-active').on('click', function () {
            var route = $(this).data('route'), active=$(this).data('active')

            ajax.set({
                url: route,
                data: 'active='+active,
                confirmTitle: '确定'+(active == 'disable' ? '禁用':'启用') +'当前用户吗?',
                method: 'PUT',
            })
            ajax.exec(function (data) {
                message = '修改失败';
                if (data.status == 1) {
                    message = '修改成功';
                }
                layer.msg(message, {
                    icon:1,
                    title:false,
                    closeBtn: false,
                    shade: 0.3,
                    end: function () {
                        location.reload()
                    }
                });
            });
        });



    })

</script>
@endsection