<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.7,maximum=1.0,user-scalable=no">
    <link rel="stylesheet" href="{{ asset('/css/biji.css') }}">
    <title>新建笔记本</title>
    <style type="text/css">
        html{
            font-size: 62.5%;
        }
        .addBook{
            text-align: center;
            margin-top: 100px;
            color: #666;
        }
        /* 超小屏幕（手机，小于 768px） */
        @media (max-width: 768px) {
            .error{

                width: 90%;
                height: 40px;
                margin: 20px 0;

            }
            .create,.cancel{
                width: 20%;
                font-size: 1.3rem;
            }

        }

        /* 小屏幕（平板，大于等于 768px） */
        @media (min-width: 768px) and (max-width: 1200px) {
            .error{

                width: 400px;
                margin-top: 20px;
            }
            .create,.cancel{
                width: 200px;
            }
        }

        /* 大屏幕（大桌面显示器，大于等于 1200px） */
        @media (min-width: 1200px) {
            .error{
                width: 500px;
                margin-top: 20px;
            }
            .create,.cancel{
                width: 250px;
            }
        }
    </style>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="addBook">
    <i class="icon Book-img"></i>
    <h5>创建笔记本</h5>
    <center>
        <form method="POST" action="{{ url('book/') }}">

            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

            <input type="hidden" name="user_id" value="{{ Auth::id() }}"/>

            <input  type="text" name="title" class="book_name form-control" placeholder="请输入笔记本名称" style="width: 37%;margin: 20px 0;font-size: 1.3rem"/>
            <a href="{{ url('/biji/') }}"><input type="button" class="cancel btn btn-default" value="取消"></a>
            <input  type="submit" class="create btn btn-primary" value="创建"/>
            <div class="error" style="text-align: left">
                @include('partials.errors')
            </div>

        </form>
    </center>
</div>
</body>
</html>