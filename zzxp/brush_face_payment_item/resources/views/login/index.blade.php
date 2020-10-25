<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/lib/html5.js"></script>
    <script type="text/javascript" src="/lib/respond.min.js"></script>
    <script type="text/javascript" src="/lib/PIE_IE678.js"></script>
    <![endif]-->
    <link href="{{ asset('static/h-ui/css/H-ui-before.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('static/h-ui/css/H-ui.login.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('lib/Hui-iconfont/1.0.8/iconfont.css') }}" rel="stylesheet" type="text/css"/>
    <!--[if IE 6]>
    <script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js"></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->

    <title>后台登录</title>
</head>

<body>
<input type="hidden" id="TenantId" name="TenantId" value=""/>
<div class="header">刷个脸系统平台系统</div>
<div class="loginWraper">
    <div id="loginform" class="loginBox">
        <form class="form form-horizontal" action="{{url('/login') }}" method="post">
            <div class="row cl">
                <label class="form-label col-3"><i class="Hui-iconfont">&#xe60d;</i></label>
                <div class="formControls col-8">
                    <input id="" name="username" type="text" placeholder="账户" class="input-text size-L">
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <div class="row cl">
                <label class="form-label col-3"><i class="Hui-iconfont">&#xe60e;</i></label>
                <div class="formControls col-8">
                    <input id="" name="password" type="password" placeholder="密码" class="input-text size-L">
                </div>
            </div>

            <div class="row cl">
                <div class="formControls col-8 col-offset-3">
                    <input class="input-text size-L" type="text" placeholder="验证码" name="vcode"
                           onblur="if(this.value==''){this.value='验证码:'}"
                           onclick="if(this.value=='验证码:'){this.value='';}" value="验证码:" style="width:150px;">
                    <img src="{{ route('vcode').'?'.time() }}" onClick="this.src=this.src+'?'+Math.random();"/>
                    (点图片刷新)
                </div>
            </div>

            <div class="row cl" style="display:none">
                <label class="form-label col-3"></label>
                <div class="formControls col-8">
                    <label class="form-label col-3">
                        <input name="type" type="radio" value="1" checked>管理员
                    </label>
                    <label class="form-label col-3">
                        <input name="type" type="radio" value="2" style="margin-left: 20px">运营商
                    </label>
                    <label class="form-label col-3">
                        <input name="type" type="radio" value="3" style="margin-left: 20px">代理商
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="formControls col-8 col-offset-3">
                    <label for="online"><input type="checkbox" name="keep_login" id="online" value="1">保持7天登录状态</label>
                </div>
            </div>

            <div class="row">
                <div class="formControls col-8 col-offset-3">
                    <input name="" type="submit" class="btn btn-success radius size-L"
                           value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
                    <input name="" type="reset" class="btn btn-default radius size-L"
                           value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="footer">Copyright</div>
<script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
{{--<script type="text/javascript" src="/js/H-ui.js"></script>--}}
</body>
</html>