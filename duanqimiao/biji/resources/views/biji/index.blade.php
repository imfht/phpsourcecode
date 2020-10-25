@extends('biji.layout')
@section('script')
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>

    <script type="text/javascript" src="{{ URL::asset('/') }}js/bootstrap.js"></script>

    <script language="JavaScript" src="{{ URL::asset('/') }}js/biji.js"></script>

    <script type="text/javascript" src="{{ URL::asset('/') }}js/jquery.zclip.js"></script>

    <script language="JavaScript" src="{{ URL::asset('/') }}js/atip.js"></script>

    <script language="JavaScript" src="{{ URL::asset('/') }}js/user.js"></script>

    <script language="JavaScript" src="{{ URL::asset('/') }}js/abook.js"></script>

    <script language="JavaScript" src="{{ URL::asset('/') }}js/copy.js"></script>

    {{--引入artDialog插件--}}
    <link rel="stylesheet" href="{{ asset('/css/ui-dialog.css') }}">

    <script src="{{ URL::asset('/') }}js/dialog-min.js"></script>
    {{--END--}}

    <script language="javascript" src="{{ URL::asset('/') }}js/sign.js"></script>

    <script language="javascript" src="{{ URL::asset('/') }}js/dropdowns.js"></script>




    <!-- 引用jquery -->
    <script src="{{ URL::asset('/') }}js/umeditor/third-party/jquery.min.js"></script>
    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}js/ueditor.all.min.js"></script>
    <!-- 选择语言 -->
    <script type="text/javascript" src="{{ URL::asset('/') }}lang/zh-cn/zh-cn.js"></script>
    <!-- 实例化编辑器代码 -->
    <script type="text/javascript">
        $(function(){
            var ue = UE.getEditor('container');
        });
    </script>

    <link rel="stylesheet" href="{{ asset('/css/biji.css') }}">

@endsection
@section('nav')
    @include('biji.partials.nav')
@endsection
@section('list')
    @include('biji.partials.list')
@endsection
@section('header')
    @include('biji.partials.header')
@endsection
@section('content')
    @include('biji.partials.content')
@endsection

{{--用户DIV--}}
<div id = "user">
    <div class="book_header">
        <a href="{{ url('/setting') }}" target="_Blank">
            <img id = "user_img" src="
            @if(empty($thumbObj->thumb))
                {{ url('images/photo.jpg') }}
             @else
            {{ url($thumbObj->thumb) }}
            @endif
                    "
                 style="width: 60px;margin:15px 0 0 68px" alt="" class="img-circle" data-toggle="tooltip" data-placement="right" title="换一张照片">
        </a>
        <center>
            <h6>{{ Auth::user()->name }}</h6>

            {{--签到模块--}}
            <!--- 签到 Field --->
            <div class="form-group">
                <p>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
                <input type="button" class="sign btn btn-primary" value="签到"/>
            </div>
            {{----END--}}
        </center>
        <hr/>
        <ul>
            <li class="li"><span class="glyphicon glyphicon-cog"></span> <a href="{{ url('/secure') }}">设置</a></li>
            <li class="li"><span class="glyphicon glyphicon-question-sign"></span> <a href="{{ url('/guide') }}">使用指南</a></li>
            <li class="li"><span class="glyphicon glyphicon-envelope"></span> <a href="{{ url('/fedBack') }}">使用反馈</a></li>
            <li class="li"><span class="glyphicon glyphicon-log-out"></span> <a href="{{ url('auth/logout') }}">退出登录</a></li>
        </ul>
    </div>
</div>
{{--END--}}

{{--笔记本DIV--}}
<div id="book">

    <div style="float: left"><h3>笔记本</h3></div>

    <div style="float: right;margin: 15px 15px;cursor: pointer">
        <a class="atip" href="{{ url('/book/create') }}" data-toggle="tooltip" data-placement="bottom" title="创建笔记本"><i class="icon addBook-img"></i></a>
    </div><br/><br/>

    <div>
        <form class="form-inline" method="GET" action="{{ url('/biji/') }}">
            <div class="form-group">
                <input type="text" id="search_book" name="search_book" class="form-control" placeholder="查找笔记本" style="width: 270px;"/>
                <button id="search_btn" type="button" class="btn btn-default">查找</button>
            </div>
        </form>
    </div>
    <div id="book_list">

    </div>
</div>
{{--END--}}

{{--搜索笔记DIV--}}
<div id="search" class="form-horizontal" role="form">
    <div style="float: left"><h3>搜索笔记</h3></div>
    <form method="GET" action="{{ url('/biji/') }}">
        <input type="text" name="search_biji" class="form-control" placeholder="按笔记内容搜索笔记" style="height: 50px;"/><br/>
        <div class="form-group">
            <div class="col-md-12">
                <label>选择笔记本</label>
                <select class="form-control" name="book_id">
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="float: right;">
            <input type="submit" value="搜索" class="btn btn-default" style="width: 100%;"/>
        </div>
    </form>
</div>
{{--END--}}

