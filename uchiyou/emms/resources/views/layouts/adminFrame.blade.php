<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="renderer" content="webkit">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', '物资管家') }}</title>

<meta name="keywords" content="">
<meta name="description" content="">

<link rel="shortcut icon" href="favicon.ico">
<link href="{{ asset('css/bootstrap.min.css?v=3.3.6') }}"
	rel="stylesheet">
<link href="{{ asset('css/font-awesome.min.css?v=4.4.0') }}"
	rel="stylesheet">
@yield('importCss')

</head>

<body class="fixed-sidebar full-height-layout gray-bg"
	style="overflow: auto">
	<div id="wrapper">
	@yield('content')
	</div>
	<!-- 全局js -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js?v=3.3.6') }}"></script>
	<script src="{{ asset('js/plugins/layer/layer.min.js') }}"></script>
	<script src="{{ asset('js/plugins/zoom/zooming.min.js') }}"></script>
	<script src="{{ asset('js/plugins/stayShape/jqthumb.min.js') }}"></script>
	<script src="{{ asset('js/plugins/stayShape/stayPictureShape.js') }}"></script>
	<script src="{{ asset('js/myGetRequest.js') }}"></script>
	<!-- 打印相关 -->
	<script src="{{ asset('js/plugins/printPDF/jquery.jqprint-0.3.js') }}"> </script>	
	<script src="{{ asset('js/plugins/printPDF/jquery-migrate1.2.1.min.js') }}"> </script>	
	<script src="{{ asset('js/plugins/printPDF/myprint.js') }}"> </script>	
	<!-- 自定义js -->
	@yield('importJs')
	<div style="text-align: center;"></div>
</body>

</html>
