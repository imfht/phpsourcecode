{__NOLAYOUT__}<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>

    <style type="text/css">
    	*{
    		padding:0;
    		margin:0;
    	}
    	
  		li {
			list-style:none;
		}
		#error_tips h2{
			background:#f9f9f9;
			background-repeat: no-repeat;
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), color-stop(25%, #ffffff), to(#f4f4f4));
			background-image: -webkit-linear-gradient(#ffffff, #ffffff 25%, #f4f4f4);
			background-image: -moz-linear-gradient(top, #ffffff, #ffffff 25%, #f4f4f4);
			background-image: -ms-linear-gradient(#ffffff, #ffffff 25%, #f4f4f4);
			background-image: -o-linear-gradient(#ffffff, #ffffff 25%, #f4f4f4);
			background-image: linear-gradient(#ffffff, #ffffff 25%, #f4f4f4);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f4f4f4', GradientType=0);
			border-bottom:1px solid #dfdfdf;
		}
    
    
		#error_tips{
			border:1px solid #d4d4d4;
			background:#fff;
			-webkit-box-shadow: #ccc 0 1px 5px;
			-moz-box-shadow: #ccc 0 1px 5px;
			-o-box-shadow:#ccc 0 1px 5px;
			box-shadow: #ccc 0 1px 5px;
			filter: progid: DXImageTransform.Microsoft.Shadow(Strength=3, Direction=180, Color='#ccc');
			width:500px;
			margin:50px auto;
		}
		#error_tips h2{
			font:bold 14px/40px Arial;
			height:40px;
			padding:0 20px;
			color:#666;
		}
		.error_cont{
			padding:20px 20px 30px 80px;
			background:url(__PUBLIC__/image/warning.gif) 32px 32px no-repeat;
			line-height:1.8;
		}
		.error_return{
			padding:10px 0 0 0;
		}
		.btn {
			color: #333;
			background:#e6e6e6 url(__PUBLIC__/image/btn.png);
			border: 1px solid #c4c4c4;
			border-radius: 2px;
			text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
			padding:4px 10px;
			display: inline-block;
			cursor: pointer;
			font-size:100%;
			line-height: normal;
			text-decoration:none;
			overflow:visible;
			vertical-align: middle;
			text-align:center;
			zoom: 1;
			white-space:nowrap;
			font-family:inherit;
			_position:relative;
			margin:0;
		}
		a.btn{
			*padding:5px 10px 2px !important;
		}
    </style>
</head>
<body>

    
    <div class="wrap">
	  <div id="error_tips">
	    <h2>错误</h2>
	    <div class="error_cont">
	      <ul>
	        <li><?php echo(strip_tags($msg));?></li>
	      </ul>
	      <div class="error_return"><a href="<?php echo($url);?>" class="btn">返回</a></div>
	    </div>
	  </div>
	</div>
    
    <script type="text/javascript">     
        	setTimeout(function(){
				location.href = '<?php echo($url);?>';
			},3000);          
    </script>
</body>
</html>
