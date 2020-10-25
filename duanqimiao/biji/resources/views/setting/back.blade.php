<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.6,maximum-scale=1.0,user-scalable=no">
    <title>使用反馈</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('/css/animate.css') }}" rel="stylesheet" type="text/css">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script language="JavaScript" src="{{ URL::asset('/') }}js/back.js"></script>
    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}

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
        .star :hover{

        }
    </style>
</head>
<body>
<div class="form-group">
</div>
<div style="margin: 50px auto;width: 500px;">
    <div style="text-align: center;margin: 20px 0;" >
        <i class="icon back-img"></i>
        <h4 style="color: #999;">使用反馈</h4>
        <h4 style="color: #666;">请根据你的使用体验，为全新的随身笔记打个分吧！</h4>
    </div>

    <div id ="start" style="color: #666;margin-top: 50px;display: inline;margin-left: 65px">
        <a class="a_star1"><img class="star1" src="{{ asset('/images/star.png') }}" onclick="click(this);" alt=""/></a>
        <a class="a_star2"><img class="star2" src="{{ asset('/images/star.png') }}" onclick="click(this);" alt=""/></a>
        <a class="a_star3"><img class="star3" src="{{ asset('/images/star.png') }}" onclick="click(this);" alt=""/></a>
        <a class="a_star4"><img class="star4" src="{{ asset('/images/star.png') }}" onclick="click(this);" alt=""/></a>
        <a class="a_star5"><img class="star5" src="{{ asset('/images/star.png') }}" onclick="click(this);" alt=""/></a>
        <br/><br/>
        <div style="display: inline;">
            <a href="{{ url('/biji/') }}"><input type="button" value="取消" class="btn btn-default" style="width: 240px;"/></a>
            <input type="button" value="完成" class="star_btn btn btn-primary"  style="width: 240px;"/>
        </div>
    </div>
</div>
</body>
</html>
