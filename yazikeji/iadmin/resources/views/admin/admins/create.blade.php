@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加管理员</legend>
        <div class="table-top-button-box">
            <a href="javascript:history.back()" class="layui-btn layui-btn-small">
                <i class="layui-icon">&#xe62d;</i> 返回
            </a>
        </div>
        <div class="layui-field-box">
            <form class="layui-form" action="{{ route('admins.store') }}" method="POST" id="admin-form">
                {!! csrf_field() !!}
                <div class="layui-form-item">
                    <label class="layui-form-label">选择角色</label>
                    <div class="layui-input-inline" style="width:30%">
                        <select name="role">
                            <option selected>请选择角色</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">管理员昵称</label>
                    <div class="layui-input-block" style="width:30%">
                        <input type="text" name="nickname"   placeholder="请输入管理员名称" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">登录邮箱</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="text" name="email"   placeholder="请输入管理员登录邮箱" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="password" name="password"   placeholder="密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="password" name="password_confirmation"   placeholder="确认密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-inline">
                        <input type="radio" name="active" value="1" title="开启" checked="">
                        <input type="radio" name="active" value="0" title="禁用">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="go">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
@endsection

@section('script')
    <script>
        layui.use(['form', 'jquery', 'ajax'], function(){

            var form = layui.form(), $ = layui.jquery, ajax = layui.ajax()

            //监听提交
            form.on('submit(go)', function(data){
                ajax.set({
                    'form' : $('#admin-form')
                });
                ajax.exec(function (data) {
                    message = '添加失败';
                    if (data.status == 1) {
                        message = '添加成功';
                    }
                    layer.msg(message, {
                        icon:1,
                        title:false,
                        closeBtn: false,
                        shade: 0.3,
                        end: function () {
                            location.href = '{{ route("admins.index") }}'
                        }
                    });
                });
                return false;
            })
        })
    </script>
@endsection