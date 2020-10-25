<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <title>个人设置</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script language="javascript" src="{{ URL::asset('/') }}js/set.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/lineChart.js"></script>

    <link rel="stylesheet" href="{{ asset('/css/biji.css') }}">
    <style type="text/css">
        html{
            font-size: 62.5%;
        }
        .info{
            font-family: gotham,helvetica,arial,sans-serif;
            color: #4a4a4a;
            font-size: 1.3rem;
            font-weight: 400;
        }
        .p{
            margin-bottom: 20px;
        }
        a{
            cursor: pointer;
        }
    </style>
</head>
<body>

<div style="margin: 0 auto;width: 300px">
    <div style="text-align: center;margin: 20px 0;" >
        <i class="icon secure-img"></i>
        <h4 style="color: #999;">账户一览</h4>
        <h3 style="color: #666;"></h3>

    </div>

    <div style="color: #666;margin:auto;padding-left:2rem ">
        <p class="p">
            用户名:<span class="info">{{ Auth::user()->name }}</span>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="{{ url('/chart/') }}">查看登录情况</a>
        </p>
        <p class="p">电子邮箱地址：<span class="info">{{ $user->email }}</span></p>
        <p class="p">注册时间：<span class="info">{{ $user->created_at->format('Y-m-d') }}</span></p>
        @if(!empty($sub))
            <p class="p">密码：<span class="info">{{ $sub }}天前修改了密码</span></p>
            <a href="/biji"><input type="button" class="btn btn-default" value="返回" style="width: 245px;margin-bottom:5px;"/></a>
        @else()
            <p class="p">密码：<span class="info">未修改过密码</span></p>
            <a href="/biji"><input type="button" class="btn btn-default" value="返回" style="width: 245px;margin-bottom:5px;"/></a>
        @endif
        <p class="p"><a class="modify_a">更改密码</a></p>
        <div class="modify_div" style="display: none">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <input type="hidden" name="_method" value="POST"/>
            原始密码：<input name="old_pass" type="password" class="form-control" placeholder="原始密码"/>
            新密码：<input name="new_pass" type="password" class="form-control" placeholder="新密码"/>
            确认密码：<input name="confirm_new_pass" type="password" class="form-control" placeholder="确认新密码"/><br/>
            <input type="button" value="确认" class="modify_pass btn btn-primary" style="width: 245px;"/><br/>
            @include('partials.errors')
        </div>
    </div>
</div>
</body>
</html>
