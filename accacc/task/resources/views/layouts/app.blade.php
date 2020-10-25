<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title','蒙太奇 - 但行好事，用心生活')</title>
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="蒙太奇,番茄工作法,待办事项,推送到kindle,RSS阅读,知乎日报订阅">
    @if(strpos($_SERVER['REQUEST_URI'],'article') !== false)
    	<meta name="referrer" content="never">
    @endif
    <!-- Fonts -->
    <link href="//cdn.bootcss.com/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
	<link href="/css/woff.css" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
		<!-- 
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
		 -->
        
        .{margin-left:0}
		.col-md-offset-1{margin-left:8.333333%}
		.col-md-offset-2{margin-left:16.666667%}
		.col-md-offset-3{margin-left:25%}
		.col-md-offset-4{margin-left:33.333333%}
		.col-md-offset-5{margin-left:41.666667%}
		.col-md-offset-6{margin-left:50%}
		.col-md-offset-7{margin-left:58.333333%}
		.col-md-offset-8{margin-left:66.666667%}
		.col-md-offset-9{margin-left:75%}
		.col-md-offset-10{margin-left:83.333333%}
		.col-md-offset-11{margin-left:91.666667%}
		.col-md-offset-12{margin-left:100%}
		
		a {
			color: #333;
		}
		body{
		    color: #525252;
		    font-family: NotoSansHans-Regular,AvenirNext-Regular,arial,Hiragino Sans GB,"Microsoft Yahei","Hiragino Sans GB","WenQuanYi Micro Hei",sans-serif;
		}
    </style>
</head>

<body id="app-layout">
	<div class="container" style="    margin-bottom: 5px;">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="{{ url('/') }}">
	        	<img src="/favicon.ico" width="30px" style="display: -webkit-inline-box;border-radius:25px;">
	            <span style="color:#429c4e">蒙太奇</span>
	         </a>
	        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			 	<span class="navbar-toggler-icon"></span>
			</button>
			
	        <div class="collapse navbar-collapse" id="navbarNav">
	        	
	          <ul class="navbar-nav  ml-auto" >
	          		@if (Auth::guest())
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}" style="color:#584029">做番茄</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/notes') }}" style="color:#4CA1D7">记想法</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/articles') }}" style="color:#F7AA55">去阅读</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/minds') }}" style="color:#0F959D">思维导图</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('help/feedback') }}" style="color:#E85205">添加反馈</a></li>
	                    @else
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}" style="color:#584029">做番茄</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/notes') }}" style="color:#4CA1D7">记想法</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/articles') }}" style="color:#F7AA55">去阅读<sup>推荐</sup></a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('/minds') }}" style="color:#0F959D">思维导图</a></li>
	                    <li class="nav-item"><a class="nav-link" href="{{ url('help/feedback') }}" style="color:#E85205">添加反馈</a></li>
	                    @endif
	                    @if (Auth::guest())
	                        <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}" style="color:#9BD6C5">登录/注册</a></li>
	                        <!-- 
	                        <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}" style="color:#9BD6C5">注册</a></li>
	                         -->
	                    @else
	                        <li class="nav-item dropdown">
	                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
	                                <span style="color:#9BD6C5">{{ Auth::user()->name }}</span> <span class="caret"></span>
	                            </a>
	
	                            <div class="dropdown-menu">
	                    			<a class="dropdown-item"  href="{{ url('statistics') }}">统计</a>
	                    			<a class="dropdown-item"  href="{{ url('settings') }}">设置</a>
	                    			<a class="dropdown-item"  href="{{ url('accounts') }}">账号管理</a>
	                    			<a class="dropdown-item"  href="{{ url('cals') }}">日历订阅</a>
	                                <a class="dropdown-item"  href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>登出</a>
	                            </div>
	                        </li>
	                    @endif
	         	 </ul>
	          </div>
		</nav>
	</div>
	
		 <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"  crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		
	    @yield('content')
	
	    <!-- JavaScripts -->
	     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	    
	    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
		<script>
		var _hmt = _hmt || [];
		(function() {
		  var hm = document.createElement("script");
		  hm.src = "https://hm.baidu.com/hm.js?d99a2953a8d7b5c51e4c84811bcbc1db";
		  var s = document.getElementsByTagName("script")[0]; 
		  s.parentNode.insertBefore(hm, s);
		})();
		</script>
		<footer class="footer  text-center">
		        <p>&copy; 2016 Congcong.us<a href="mailto:accacc@126.com?subject=MontageGTD反馈">遇到问题?联系我~</a></p>
		</footer>
</body>
</html>
