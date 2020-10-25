<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>笔记信息</title>
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>
    <script type="text/javascript" src="{{ URL::asset('/') }}js/bootstrap.js"></script>
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
        <div style="margin: 20px 0; text-align: center;" >
            <span style="font-size: 35px;color: #666;margin: 20px 0;" class="glyphicon glyphicon-info-sign"></span>
            <h4 style="color: #999;">笔记本信息</h4>

            {{--确认删除--}}
            <div class="modal fade" id="modal-delete" tabIndex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                ×
                            </button>
                            <h4 class="modal-title">提示</h4>
                        </div>
                        <div class="modal-body">
                            <p class="lead">
                                <i class="fa fa-question-circle fa-lg"></i>
                                您确定要删除笔记本{{ $book->title }}以及所属的所有笔记?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="{{ url('/book/'.$book->id) }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-times-circle"></i> Yes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ url('/book/'.$book->id) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <input type="hidden" name="_method" value="PUT"/>


                <div style="color: #666;margin-top: 50px;">

                    <div class="input-group" style="margin-bottom: 50px;">
                        <span class="input-group-addon">标题</span>
                        <input type="text" class="form-control"  aria-describedby="basic-addon2" name="title" value="{{ $book->title }}">
                     <span class="input-group-btn">
                           <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete">
                               <i class="fa fa-times-circle"></i>
                               删除笔记本
                           </button>
                     </span>
                    </div>
                    <div style="text-align: left;">
                        <p class="p">创建时间：<span class="info">{{ $book->created_at }}</span></p>
                        <p class="p">更新时间：<span class="info">{{ $book->updated_at }}</span></p>
                        <p class="p">创建者：<span class="info">{{ Auth::user()->name }}</span></p>
                        <div>
                            <a href="{{ url('/biji/') }}"><input type="button" value="取消" class="btn btn-default" style="width: 240px;"/></a>
                            <input type="submit" value="保存修改" class="btn btn-primary"  style="width: 240px;"/>
                        </div>
                    </div>

                </div>
            </form>
            </div>
        </div>
</body>
</html>
