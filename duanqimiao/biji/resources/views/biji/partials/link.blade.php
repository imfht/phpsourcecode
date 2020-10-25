<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <title>笔友 | Be yourself</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    @yield('script')

    <style type="text/css">
        .bs-callout {
            padding: 20px;
            margin: 20px 20px;
            border: 1px solid #eee;
            border-left-width: 5px;
            border-radius: 3px;
        }
        .bs-callout-info {
            border-left-color: #5bc0de;
        }
    </style>
</head>
<body>
    <div style="text-align: right">
        <ul class="list-group">
            <li class="list-group-item list-group-item-info">笔友 | Be yourself</li>
        </ul>
    </div>

    <blockquote class="bs-callout bs-callout-info">
        <p>
            {!! $biji->content !!}
        </p>
        <footer>{{ $biji->title }}</footer>
    </blockquote>


</body>
</html>