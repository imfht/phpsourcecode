<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>废纸篓</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/biji.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/bootstrap.js"></script>
    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}

    <style type="text/css">
        html{
            font-size: 62.5%;
        }
        body{
            overflow:hidden;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<form class="form-horizontal" role="form" >
    <div  class="col-md-4 col-md-offset-1" style="margin-bottom: 1em;" ><h3><a style=";color: #666" href="/biji/">主页</a> <small>» <span class="glyphicon glyphicon-trash"></span> 废纸篓</small></h3></div>
    <div class="container">
        <table class="table table-condensed table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>标题</th>
                <th>操作</th>
            </tr>
            </thead>

            <tbody class="tbody">
            @foreach($wastebasket as $basket)
                <tr>
                    <td>{{ $id++ }}</td>
                    <td>{!! $basket->title !!}</td>
                    <td>
                        <input type="hidden" name="basket-id" value="{{ $basket->id }}"/>
                        <a class="btn btn-sm btn-primary recover-btn">
                            <i class="fa fa-eye"></i>还原
                        </a>
                        <a><button id="collect-delete-btn" type="button" class="btn btn-danger btn-sm btn-clear">彻底删除</button></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</form>
</body>
</html>