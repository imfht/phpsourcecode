@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>修改密码</legend>

        <div class="layui-field-box">
            <form class="layui-form" action="{{ route('admin.update.password') }}" method="POST" id="update-password-form">
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
                <div class="layui-form-item">
                    <label class="layui-form-label">原密码</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="password" name="oldPassword"   placeholder="原密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">新密码</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="password" name="password"   placeholder="新密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">确认新密码</label>
                    <div class="layui-input-inline" style="width:30%">
                        <input type="password" name="password_confirmation"   placeholder="确认新密码" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="go">修改密码</button>
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
                    form : $('#update-password-form'),
                    confirmTitle: '确定要修改密码吗?'
                });
                ajax.exec(function (data) {
                    if (data.status == 1) {
                        message = '密码更新成功';
                    } else {
                        message = data.message;
                    }
                    layer.msg(message, {
                        icon:1,
                        title:false,
                        closeBtn: false,
                        shade: 0.3,
                        end: function () {
                            location.reload();
                        }
                    });
                });
                return false;
            })
        })
    </script>
@endsection