<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>新建笔记</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/bootstrap.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/dropdowns.js"></script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.all.min.js"></script>
    <!-- 选择语言 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}lang/zh-cn/zh-cn.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container');
    </script>

    <script language="javascript" src="{{ URL::asset('/') }}js/sweetalert.js"></script>


    <link rel="stylesheet" type="text/css" href="{{ asset('/css/sweetalert.css') }}">

    <style type="text/css">
        html{
            font-size: 62.5%;
        }
        body{
            overflow:hidden;
            margin-top: 20px;
        }
        div{
            width:90%;
        }
    </style>
</head>
<body>

    <form class="form-horizontal" role="form" method="POST" action="{{ url('/biji/') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="published_at" value="{{ Carbon\Carbon::now() }}"/>
        <h3 class="col-md-4 col-md-offset-2" ><span class="glyphicon glyphicon-list-alt"></span> 新建笔记</h3>
        <div style="margin-left: 0.2rem">
            <input type="hidden" name = "user_id" value={{ Auth::id() }}/>
        </div>
        <div class="form-group" style="margin-left:0.3em;">
            <div class="col-md-3 col-md-offset-9">
                <label>选择笔记本</label>
                <select class="form-control" name="book_id">
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-left: 0.2rem">
            <div class="col-md-10 col-md-offset-2">
                <input name="title" type="text" class="form-control" value="默认无标题" onfocus="if (value =='默认无标题'){value =''}" onblur="if (value ==''){value='默认无标题'}"/>
            </div>
        </div>
        <div class="form-group" style="margin-left: 1rem">
            <div class="col-md-offset-2" >
                <!-- 加载编辑器的容器 -->
                <script id="container" name="content" type="text/plain" style="max-height: 300px;overflow-y: auto;overflow-x: hidden;min-width:320px;"></script>
            </div>
        </div>
        <div class="col-md-offset-2" style="display: inline;" >
                <button type="submit" class="btn btn-primary" style="width: 150px">保存</button>
                <a href="{{ url('/biji') }}"><button type="button" class="btn btn-default" style="width: 150px">取消</button></a>
        </div>
        <div class="form-group">
            <div class="col-md-9 col-md-offset-2">
                @include('partials.errors')
            </div>
        </div>
    </form>
</body>
</html>