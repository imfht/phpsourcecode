<!DOCTYPE html>
<html>	
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>{{ $website['title'] }}-{{@$website['root']['sysfullname']?@$website['root']['sysfullname']:trans('admin.website_name')}}-{{@$website['root']['systitle']?@$website['root']['systitle']:trans('admin.website_type')}}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<meta name="keywords" content="{{$website['root']['syskeywords']}}" >
<meta name="description" content="{{$website['root']['sysdescription']}}" >
<link rel="shortcut icon" href="/favicon.ico" >
<link href="{{asset('/module/login/css/style.css')}}" rel='stylesheet' type='text/css' />
@if(env('RESOURCES_ASSETS_ENV','dev')=='dev')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@else
<link rel="stylesheet" href="{{ elixir('css/login.css') }}">
@endif
<!--webfonts
<link href='http://fonts.useso.com/css?family=PT+Sans:400,700,400italic,700italic|Oswald:400,300,700' rel='stylesheet' type='text/css'>
<link href='http://fonts.useso.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
-->
<!--//webfonts-->
<script src="{{asset('/module/jquery/dist/jquery.min.js')}}"></script>
<!--layer-->
<script src="{{asset('/module/layer/layer.js')}}"></script>
<!--common-->
@if(env('RESOURCES_ASSETS_ENV','dev')=='dev')
<script src="{{asset('js/login.js')}}"></script>
@else
<script src="{{elixir('js/login.js')}}"></script>
@endif
</head>
<body>
	<script>$(document).ready(function(c) {
		$('.close').on('click', function(c){
			$('.login-form').fadeOut('slow', function(c){
				$('.login-form').remove();
			});
		});	  
	});
	</script>
	@yield('content')
	<div class="copy-rights">
			<p>{{ $website['copyrights'] }}</p>
	</div>
</body>
</html>