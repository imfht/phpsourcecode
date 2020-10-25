<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>分享笔记</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        .info{
            font-family: gotham,helvetica,arial,sans-serif;
            color: #4a4a4a;
            font-size: 13px;
            font-weight: 400;
        }
        .p{
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div style="margin: 0 auto;width: 500px;">
    <div style="text-align: center;margin: 20px 0;" >
        <img src="{{ asset('/images/email.png') }}" alt=""/>
        <h4 style="color: #999;">发送邮件</h4>
    </div>

    <div style="color: #666;margin-top: 50px;">
        <form class="form-horizontal" role="form" method="GET" action="{{ url('/send') }}">
            @include('partials.errors')

            <input type="hidden" name="content" value="{{ $biji->content }}"/>{{--未解析的笔记内容(html)--}}

            <div class="input-group">
                <span class="input-group-addon">收件人</span>
                <input name="sendTo" type="email" class="form-control"  aria-describedby="basic-addon2"/>
            </div><br/>
            <textarea name="message" class="form-control" rows="3" placeholder="输入消息..."></textarea><br/>
            <h5>共享笔记：{{ $biji->title }}</h5>
            <div style="display: inline">
                <a href="{{ url('/biji/') }}"><input type="button" value="取消" class="btn btn-default" style="width: 245px;"/></a>
                <input type="submit" value="发送" class="btn btn-primary" style="width: 245px;"/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
