@extends('layout')
@section('title', $title)
@section('style')

@stop
@section('body')
    <blockquote class="layui-elem-quote">{{$title}}</blockquote>
    <div class="layui-form-item">
        <a class="layui-btn ml5" layTips="点击返回列表|3|#3595CC" href="{{route('list')}}"><i
                    class="fa fa-plus-circle fa-fw"></i>返回列表</a>
    </div>
    <div class="layui-collapse">
        <div class="layui-colla-item ">
            <h2 class="layui-colla-title">帮助</h2>
            <div class="layui-colla-content layui-show">
                <ul>
                    <li><span class="layui-badge layui-bg-green mr5">1</span>带*号的必填</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <input type="text" name="order_sn" class="layui-input" placeholder="订单号">
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="mobile" class="layui-input" placeholder="11位手机号">
                </div>
                <div class="layui-input-inline">
                    <select name="order_status">
                        <option value="-1">选择订单状态</option>
                        <option value="1">新订单</option>
                        <option value="2">完成订单</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="pay_status">
                        <option value="-1">选择付款状态</option>
                        <option value="1">付款成功</option>
                        <option value="2">付款中</option>
                        <option value="3">付款失败</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <button class="layui-btn" lay-submit lay-filter="success">搜索</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script type="text/javascript">

        layui.use(['form', 'layer', 'table', 'element'], function () {
            // 操作对象
            var form = layui.form
                , layer = layui.layer
                , $ = layui.jquery;

            $(document).keyup(function (event) {
                if (event.keyCode === 13) {
                    $("button").trigger("click");
                }
            });
            // 验证
            form.verify({
                username: function (value) {
                    if (value === "") {
                        return "请输入用户名";
                    }
                },
                password: function (value) {
                    if (value === "") {
                        return "请输入密码";
                    }
                },
                captcha: function (value) {
                    if (value === "") {
                        return "请输入验证码";
                    }
                }
            });
            // 提交监听
            form.on('submit(success)', function (data) {
                layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>
@stop