<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A beautiful laravel log viewer">
    <meta name="author" content="gouguoyin">
    <meta name="qq" content="245629560@qq.com">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('log-viewer::title')</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="https://cdn.staticfile.org/metisMenu/3.0.5/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs/jq-3.3.1/dt-1.10.20/fh-3.1.6/r-2.2.3/datatables.min.css" rel="stylesheet">

    <!-- FontAwesome Fonts -->
    <link href="https://cdn.staticfile.org/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        body {
            background-color: #f8f8f8;
        }
        button {
            margin-right: 3px;
        }
        #wrapper {
            width: 100%;
        }
        #page-wrapper {
            padding: 0 15px;
            min-height: 568px;
            background-color: white;
        }
        .page-header .header-btns {
            float: right;
            position: relative;
            top: -40px;
        }

        .panel-body .list-inline:last-child{
            margin-bottom: -10px;
        }

        @media (min-width: 768px) {
            #page-wrapper {
                position: inherit;
                margin: 0 0 0 300px;
                padding: 0 30px;
                border-left: 1px solid #e7e7e7;
            }
        }
        .navbar-top-links {
            margin-right: 0;
        }
        .navbar-top-links li {
            display: inline-block;
        }
        .navbar-top-links li:last-child {
            margin-right: 15px;
        }
        .navbar-top-links li a {
            padding: 15px;
            min-height: 50px;
        }
        .navbar-top-links .dropdown-menu li {
            display: block;
        }
        .navbar-top-links .dropdown-menu li:last-child {
            margin-right: 0;
        }
        .navbar-top-links .dropdown-menu li a {
            padding: 3px 20px;
            min-height: 0;
        }
        .navbar-top-links .dropdown-menu li a div {
            white-space: normal;
        }
        .navbar-top-links .dropdown-messages,
        .navbar-top-links .dropdown-tasks,
        .navbar-top-links .dropdown-alerts {
            width: 310px;
            min-width: 0;
        }
        .navbar-top-links .dropdown-messages {
            margin-left: 5px;
        }
        .navbar-top-links .dropdown-tasks {
            margin-left: -59px;
        }
        .navbar-top-links .dropdown-alerts {
            margin-left: -123px;
        }
        .navbar-top-links .dropdown-user {
            right: 0;
            left: auto;
        }
        .sidebar .sidebar-nav.navbar-collapse {
            padding-left: 0;
            padding-right: 0;
        }
        .sidebar .sidebar-search {
            padding: 15px;
        }
        .sidebar ul li {
            border-bottom: 1px solid #e7e7e7;
        }
        .sidebar ul li a.active {
            border: 1px solid #777;
            background-color: #f5f5f5;
        }
        .sidebar .arrow {
            float: right;
        }
        .sidebar .fa.arrow:before {
            content: "\f105";
        }
        .sidebar .active > a > .fa.arrow:before {
            content: "\f107";
        }
        .sidebar .nav-second-level li,
        .sidebar .nav-third-level li {
            border-bottom: none !important;
        }
        .sidebar .nav-second-level li a {
            padding-left: 37px;
        }
        .sidebar .nav-third-level li a {
            padding-left: 52px;
        }
        .sidebar .search-group {
            width: 100%;
        }
        @media (min-width: 768px) {
            .sidebar {
                z-index: 1;
                position: absolute;
                overflow: auto;
                max-height: 600px;
                width: 300px;
                margin-top: 51px;
            }
            .navbar-top-links .dropdown-messages,
            .navbar-top-links .dropdown-tasks,
            .navbar-top-links .dropdown-alerts {
                margin-left: auto;
            }
        }

        .text-success {
            color: #3c763d;
        }
        .text-danger {
            color: #a94442;
        }
        .text-notice {
            color: bisque;
        }
        .text-warning {
            color: #f7be57;
        }
        .text-debug {
            color: #8e8c8c;
        }
        .text-alert {
            color: #4ba4ea;
        }

        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc,
        table.dataTable thead .sorting_asc_disabled,
        table.dataTable thead .sorting_desc_disabled {
            background: transparent;
        }
        table.dataTable thead .sorting_asc:after {
            content: "\f0de";
            float: right;
            font-family: fontawesome;
        }
        table.dataTable thead .sorting_desc:after {
            content: "\f0dd";
            float: right;
            font-family: fontawesome;
        }
        table.dataTable thead .sorting:after {
            content: "\f0dc";
            float: right;
            font-family: fontawesome;
            color: rgba(50, 50, 50, 0.5);
        }
        .show-grid [class^="col-"] {
            padding-top: 10px;
            padding-bottom: 10px;
            border: 1px solid #ddd;
            background-color: #eee !important;
        }

    </style>

</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">Laravel Log Viewer v1.0</a>
        </div>
        <!-- /.navbar-header -->
        <ul class="nav navbar-top-links navbar-right">
            @foreach(config('log-viewer.web_navbar') as $nav => $url)
            <li><a href="{{ $url }}" target="_blank">{{ $nav }}</a></li>
            @endforeach
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">
                    @guest
                    <i class="fa fa-user fa-fw"></i>
                    @else
                    {{ Auth::user()->name }}
                    @endguest
                    <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">

                    <li><a target="_blank" href="https://github.com/gouguoyin/laravel-log-viewer"><i class="fa fa-fw fa-file-text-o"></i> {{ trans('log-viewer::log-viewer.dropdown.document_label') }}</a></li>

                    @guest
                        @if (Route::has('register'))
                        <li><a href="{{ route('register') }}"><i class="fa fa-fw fa-user"></i> {{ trans('log-viewer::log-viewer.dropdown.register_label') }}</a></li>
                        @endif

                        @if (Route::has('login'))
                        <li><a href="{{ route('login') }}"><i class="fa fa-fw fa-sign-in"></i> {{ trans('log-viewer::log-viewer.dropdown.login_label') }}</a></li>
                        @endif
                    @else
                    <li class="divider"></li>
                    @if (Route::has('logout'))
                    <li>
                        <a href="javascript:;" onclick="event.preventDefault();$('#logout-form').submit();"><i class="fa fa-sign-out fa-fw"></i> {{ trans('log-viewer::log-viewer.dropdown.logout_label') }}</a>
                        <form class="hide" id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    </li>
                    @endif
                    @endguest
                </ul>
            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group search-group">
                            <form method="get" action="{{ route('log-viewer-home') }}" role="form">
                                <div class="form-group input-group">
                                    <input name="keywords" type="text" class="form-control" value="{{ $keywords }}" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>

                            </form>
                        </div>
                        <!-- /input-group -->
                    </li>

                    @foreach ($service->getAllLogs($keywords) as $log)
                        <li>
                            <a class="{{ $service->getLogName() == $log ? 'active': ''}}" href="{{ route('log-viewer-home')}}?file={{ $log }}"><i class="fa fa-fw fa-files-o"></i> {{ $log }}</a>
                        </li>
                    @endforeach

                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <div id="page-wrapper">
        @yield('log-viewer::content')
    </div>

</div>
<!-- /#wrapper -->

@section('log-viewer::script')
    <!-- jQuery -->
    <script src="https://cdn.staticfile.org/jquery/3.1.0/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Datatable Plugin JavaScript -->
    <script src="https://cdn.datatables.net/v/bs/jq-3.3.1/dt-1.10.20/fh-3.1.6/r-2.2.3/datatables.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="https://cdn.staticfile.org/metisMenu/3.0.5/metisMenu.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
    <script src="//cdn.staticfile.org/artDialog/7.0.0/dialog-plus.js"></script>

    <script>
        /**
         * 优化的弹出框
         * @param msg 弹出框消息
         * @param type 消息类型
         * @param callbak 回调函数
         */
        function alert(msg, type, callbak) {
            var shift;
            var time;
            if(!msg){
                return;
            }

            if(type == 'success'){
                shift = 5;
                time  = 1;
            }else if(type == 'error'){
                shift = 6;
                time  = 2;
            }else{
                shift = 6;
                time  = 2;
            }

            if(callbak == ''){
                callbak = function () {

                };
            }

            time = time * 1000;
            layer.msg(msg, {time:time, shift:shift}, callbak);
        }

        /**
         * 优化的确认框
         * @param msg 确认框消息
         * @param callback 确认回调函数
         */
        function confirm(msg, callback) {
            var d = dialog({
                fixed: true,
                width: '280',
                title: "{{ trans('log-viewer::log-viewer.confirm.confirm_title') }}",
                content: msg,
                lock: true,
                opacity: .1,
                okValue: "{{ trans('log-viewer::log-viewer.confirm.ok_label') }}",
                ok: function () {
                    if(typeof callback === "function") {
                        callback();
                        return true;
                    }
                    return false;
                },
                cancelValue: "{{ trans('log-viewer::log-viewer.confirm.cancel_label') }}",
                cancel: function () {
                    d.close().remove();
                    return false;
                }
            });

            d.showModal();
            return false;
        }
    </script>
@show

</body>

</html>
