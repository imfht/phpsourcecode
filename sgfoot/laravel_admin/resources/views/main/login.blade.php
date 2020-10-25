@extends('layout')
@section('title', '登陆中心')
@section('style')
    <link rel="stylesheet" href="{{asset('frame/static/css/supersized.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
@stop
@section('body')
    <div class="body supersized">
        <div class="login">
            <h1>管理员登录</h1>
            <div class="layui-form">
                <div class="layui-form-item">
                    <input class="layui-input" name="username" placeholder="用户名" value="sgfoot.com"
                           lay-verify="required"
                           lay-verType="tips" type="text" autocomplete="off">
                </div>
                <div class="layui-form-item">
                    <input class="layui-input" name="password" placeholder="密码" value="sgfoot.com" lay-verify="required"
                           lay-verType="tips" type="password" autocomplete="off">
                </div>
                <div class="layui-form-item form_code">
                    <input class="layui-input" style="width: 140px;" name="captcha" placeholder="验证码"
                           lay-verify="required"
                           lay-verType="tips" type="text" autocomplete="off">
                    <div class="code"><img id="captcha" src="{{captcha_src()}}" width="116" height="36"
                                           onclick="this.src='{{captcha_src()}}&'+Math.random()"></div>
                </div>
                <button class="layui-btn login_btn" lay-submit="" lay-filter="login">登录</button>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{asset('frame/static/js/supersized.3.2.7.min.js')}}"></script>
    <script type="text/javascript">
        layui.use(['form', 'layer'], function () {
            //$("#captcha").attr("src", '/captcha/default?' + Math.random());//刷新验证码 的
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
            form.on('submit(login)', function (data) {
                $.post('{{route('login')}}', data.field, function (rev) {
                    if (rev.status === 0) {
                        window.location.href = rev.data;
                    } else {
                        $("input[name='captcha']").select().focus();
                        layer.msg(rev.msg, {shift: 1, icon: 2});
                        $("#captcha").attr("src", '{{captcha_src()}}?' + Math.random());//刷新验证码 的
                    }
                });
                return false;
            });
            $.supersized({
                // Functionality
                slide_interval: 4000,    // Length between transitions
                transition: 1,    // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
                transition_speed: 1000,    // Speed of transition
                performance: 1,    // 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)

                // Size & Position
                min_width: 0,    // Min width allowed (in pixels)
                min_height: 0,    // Min height allowed (in pixels)
                vertical_center: 1,    // Vertically center background
                horizontal_center: 1,    // Horizontally center background
                fit_always: 0,    // Image will never exceed browser width or height (Ignores min. dimensions)
                fit_portrait: 1,    // Portrait images will not exceed browser height
                fit_landscape: 0,    // Landscape images will not exceed browser width

                // Components
                slide_links: 'blank',    // Individual links for each slide (Options: false, 'num', 'name', 'blank')
                slides: [    // Slideshow Images
                    {image: '../frame/static/image/1.jpg'},
                    {image: '../frame/static/image/2.jpg'},
                    {image: '../frame/static/image/3.jpg'}
                ]
            });
        });
    </script>
@stop