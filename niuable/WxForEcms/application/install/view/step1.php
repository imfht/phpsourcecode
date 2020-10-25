<?php defined('ROOTPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$title?></title>
	<script src="<?php echo url('','',false)?>/../../../jquery-2.1.4/jquery.min.js"></script>
	<link href="<?php echo url('','',false)?>/../../../css/bootstrap.min.css" rel="stylesheet">
	<script src="<?php echo url('','',false)?>/../../../js/bootstrap.min.js"></script>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container-fluid text-center" >
	<div class="row" style="margin-top:20px;">
		欢迎使用WXForEcmsByTP5 本安装程序将引导您完成安装
	</div>
	<div class="row">
		<h2>用户使用协议</h2>
		<div class="hidden-xs col-md-4"></div>
		<div class="col-md-4 text-left bg-info">		
			<p>1：本系统版本是开源免费版，未经作者允许不得私自销售等有损作者版权的行为。</p>
			<p>2：不保证后续版本调整收费模式和收费内容。<br></p>
			<p>3：本系统具有较强的兼容性，但难以保证一定适合您的网站环境，请自行测试使用。</p>
			<p>4：系统使用了众多第三方开源工具，如百度ueditor，需要一并遵循它们的协议内容</p>
			<p>5：不得用于违法内容，违反该条产生的后果自行承担</p>
			<p>6：本协议仅限于本软件的本版本，协议随同软件一起分发</p>
			<p>7：本软件产权所有人王维，官方沟通Email：hi.wangwei@qq.com，软件所有人将根据需要建立其他交流平台，相关信息将由上述邮箱发出或后续版本公告。</p>
			<p>8：由于网络环境的极大不确定性，本软件作者不承担使用本软件及其派生品产生的任何损失或危害。</p>
			<p>9：产权所有人视发展需要建立开发组、变更产权，义务进行软件的维护升级等，但不具有法律强制责任</p>
			<p>10：本协议未尽之处遵循中华人民共和国知识产权保护相关法律</p>
		</div>
		<div class="hidden-xs col-md-4"></div>
	</div>
	<div class="row" style="margin-top:20px;">
		<a href="javascript:void(0)" class="btn btn-danger" onClick="window.close()" target="_self">拒绝</a>
		<a href="<?php echo url('/index/index/wangwei/2','',false)?>" class="btn btn-success" target="_self">同意</a>
	</div>
</div>
</body>
</html>