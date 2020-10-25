<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>笔记共享</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/circle.css') }}">
</head>
<body>

<div style="margin: 0 auto;width: 500px;">
    <div style="text-align: center;margin: 50px 0;" >
        <i class="icon share-img"></i>
        <h4 style="color: #999;">笔记共享</h4>
        <h3 style="color: #666;">{{ $biji->title }}</h3>
        <h6>作者：{{ Auth::user()->name }}</h6><br/>
        <form method="POST" action="{{ url('/circle') }}" class="form-horizontal" role="form">
            <div style="text-align: left;">@include('partials.errors')</div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <input type="hidden" name="biji_id" value="{{ $biji->id }}"/>
            <div class="input-group" >
                <span class="input-group-addon">标签</span>
                <input name="tag" type="text" class="form-control" placeholder="注：如有多个标签用空格隔开 " aria-describedby="basic-addon2" >
            </div><br/>
            <div class="form-group">
                <a href="{{ url('/biji/') }}"><input type="button" value="取消" class="btn btn-default" style="width: 245px;"/></a>
                <input type="submit" value="确认分享" class="btn btn-primary" style="width: 245px;"/>
            </div>
        </form>
    </div>
</div>
</body>
</html>
