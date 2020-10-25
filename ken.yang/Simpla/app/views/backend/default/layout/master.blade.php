<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="/favicon.ico" />

        <title>Simpla|后台管理控制面板</title>

        <!-- Bootstrap Core CSS -->
        <link href="{{BACK_THEME_STATIC}}/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="{{BACK_THEME_STATIC}}/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

        <!-- Timeline CSS -->
        <link href="{{BACK_THEME_STATIC}}/css/plugins/timeline.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="{{BACK_THEME_STATIC}}/css/sb-admin-2.css" rel="stylesheet">

        <!-- Morris Charts CSS -->
        <link href="{{BACK_THEME_STATIC}}/css/plugins/morris.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="{{BACK_THEME_STATIC}}/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <link href="{{BACK_THEME_STATIC}}/css/main.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.{{BACK_THEME_STATIC}}/js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- jQuery Version 1.11.0 -->
        <script src="{{BACK_THEME_STATIC}}/js/jquery-1.11.0.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="{{BACK_THEME_STATIC}}/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="{{BACK_THEME_STATIC}}/js/plugins/metisMenu/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="{{BACK_THEME_STATIC}}/js/sb-admin-2.js"></script>

        <!--按钮提示框插件-->
        <script src="{{BACK_THEME_STATIC}}/plugs/bootstrap-confirmation/bootstrap-confirmation.js"></script>

    </head>

    <body>

        <div id="wrapper">
            @include('BackTheme::layout.header')
            <div id="page-wrapper">
                @yield('content')
                @include('BackTheme::layout.footer')
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

        <!-- 配置文件 -->
        <script type="text/javascript" src="{{BACK_THEME_STATIC}}/plugs/ueditor/ueditor.config.js"></script>
        <!-- 编辑器源码文件 -->
        <script type="text/javascript" src="{{BACK_THEME_STATIC}}/plugs/ueditor/ueditor.all.js"></script>
        <!-- 实例化编辑器 -->
        <script type="text/javascript">
            var ue = UE.getEditor('ueditor');
        </script>
        <script src="{{BACK_THEME_STATIC}}/js/main.js"></script>
    </body>

</html>
