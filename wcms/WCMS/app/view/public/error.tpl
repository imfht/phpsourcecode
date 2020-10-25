<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8" />
	<title>WCMS 登录</title>
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->
	<link href="./static/bootstrap2/css/bootstrap.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/style.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/style-responsive.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/retina.css" rel="stylesheet" />
		<link href="./static/bootstrap2/css/my.less" rel="stylesheet/less" />
		<script type="text/javascript" src="./static/public/less.min.js" ></script>	<!-- end: CSS -->	<!-- end: CSS -->
 <body>
		
	
		
		 <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>{$error}!</h1>
        <p>当你看到这个页面，说明你没有权限进入</p>
        <p><a href="./" class="btn btn-primary btn-large">返回首页 &raquo;</a>    <a href="./index.php?anonymous/login" class="btn btn-success btn-large">登录 &raquo;</a></p>
      </div>

     

      <hr>

      <footer>
        <p>&copy; {$config.copyright}</p>
      </footer>

    </div> <!-- /container -->
  </body>
</html>