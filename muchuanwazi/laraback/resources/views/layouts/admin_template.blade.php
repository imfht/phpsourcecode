<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $page_title or "" }} {{Config::get('system.sys_name').'@'.Config::get('system.sys_version')}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{ asset("/bower_components/adminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="{{ asset("/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="{{ asset("/css/ionicons.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/adminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link href="{{ asset("/bower_components/adminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />
@stack('cssFiles')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue">
<div class="wrapper">

    <!-- Header -->
    @include('layouts.header')

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{ $page_title or "" }}
                <small>{{ $page_description or null }}</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('layouts.footer')

</div><!-- ./wrapper -->



<script src="{{ asset ('/bower_components/adminLTE/plugins/jQuery/jQuery-2.2.3.min.js') }}"></script>
<script src="{{ asset ('/bower_components/adminLTE/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ asset ('/bower_components/adminLTE/dist/js/app.min.js') }}" type="text/javascript"></script>
@stack('scriptFiles')
@stack('runningScripts')
</body>
</html>