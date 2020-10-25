<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title}</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/admin/css/frame.css" />
<script type="text/javascript" src="__PUBLIC__/js/do.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/config.js"></script>
</head>
<body class="login_body" scroll="no">
<div class="login_title">注册{$title}</div>
<form class="t-form" enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="">
<div class="login_main">
     <div class="login_box">
	  <div class="login_do" id="tips" style="display:none"> </div>
      <div style="padding:15px 20px;">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tbody>
			  <tr>
				<th>用户名：</th>
				<td><input class="login_input" type="text" name="username" ajaxurl="{url('web/validusername')}" datatype="s1-30" nullmsg="请输入用户名！" errormsg="名称至少1个字符,最多30个字符！"><span style="color:#fff">必须填写</span>
				</td>
			  </tr>
			  <tr>
				<th>密&nbsp;&nbsp;&nbsp;码：</th>
				<td><input name="password" class="login_input" type="password" /></td>
			  </tr>
			  <tr>
				<th>再输密码：</th>
				<td><input name="passwordagain" class="login_input" type="password" /></td>
			  </tr>
			  <tr>
				<th>验证码：</th>
				<td><div>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td width="120"><input name='checkcode' class="login_yz" type="text" /></td>
						<td><img src="{url('index/verify')}" width="100" height="32" title="如果您无法识别验证码，请点图片更换"  id="verifyImg" style="cursor:pointer" /></td>
					  </tr>
					</table>
				  </div>
				</td>
			  </tr>
			  <tr>
				<th>&nbsp;</th>
				<td>
					<input class="button" value="注册" type="submit">
				</td>
			  </tr>
			</tbody>
		  </table>
		</div>
    </div>
</div>
</form>
<div class="login_footer">
  <p>{$footer}</p>
</div>
<script type="text/javascript">
Do.ready('base','form', function(){
$(".t-form").Form({});

$("#verifyImg").click(function(){
		var url = $(this).attr('src');
		url = url + ((/\?/.test(url)) ? '&' : '?' ) + new Date().getTime();
		$(this).attr('src', url)
	});
});
</script>
</body>
</html>
