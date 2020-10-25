<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="x-ua-compatible" content="text/html;" />
		<title>LzCMS-博客版</title>
		<meta name="Keywords" content="LzCMS-博客版" />
		<meta name="Description" content="LzCMS-博客版" />
		<link rel="stylesheet" type="text/css" href="/static/css/install_style.css"/>
	</head>
	<body>
		<div class="content">
			<div class="con-head">
				<span class="update fr"><?php echo LZ_VERSION?></span>
			</div>
			<!--安装步骤-->
			<div class="con-body">
				<div class="step-con">
					<!--步骤-->
					<div class="step-box ">
						<span class="step-num  bg-img s1_on fl"></span>
						<span class="step-num  bg-img s2_on fl"></span>
						<span class="step-num  bg-img s3_off fl"></span>
					</div>
				</div>
					<div class="clear"></div>
					<!--数据库信息-->
					<form action='<?php echo url('setup3')?>' method='get' onsubmit="return check_form();">
					<div class="Test2">
						<table>
							<!--一行-->
							<tr>
								<th>数据库信息</th>
							</tr>
							<tr>
								<td class="text2-td1">服务器地址：</td>
								<td><input type="text"  name='db_address' value='127.0.0.1' /></td>
								<td>数据库服务器地址，一般为127.0.0.1</td>
							</tr>
							<tr>
								<td class="text2-td1">数据库端口：</td>
								<td><input type="text"  name='db_port' value='3306' /></td>
								<td>系统数据库端口，一般为3306</td>
							</tr>
							<tr>
								<td class="text2-td1">数据库名称：</td>
								<td><input type="text"  name='db_name' autofocus="autofocus" /></td>
								<td>系统数据库名,必须包含字母</td>
							</tr>
							<tr>
								<td class="text2-td1">用户名：</td>
								<td><input type="text"  name='db_user' /></td>
								<td>连接数据库的用户名</td>
							</tr>
							<tr>
								<td class="text2-td1">密码：</td>
								<td><input type="password" name='db_pwd' /></td>
								<td>连接数据库的密码</td>
							</tr>
							<tr>
								<td class="text2-td1">数据库前缀：</td>
								<td><input type="text"  name='db_pre'  value='lz_' /></td>
								<td>建议使用默认,数据库前缀必须带 '_'</td>
							</tr>

						</table>
						<div class="agree-btn2 fl checkmysql" onclick="check_mysql();">检测数据库连接</div>
						<div id="error_p" class="notic-red fl"></div>
						<div id="right_p" class="notic-green fl"></div>
						<!--end-->
					</div>
					<div class="bline Test2 admin_block">
						<table>
							<!--第一个表单-->
							<tr>
								<th>站点信息配置</th>
							</tr>
							<tr>
								<td class="text2-td1">站点名称：</td>
								<td><input type="text" name="site_name" /></td>
								<td>站点名称不能为空</td>
							</tr>
							<tr>
								<td class="text2-td1">站点关键词：</td>
								<td><input type="text" name="site_keywords" /></td>
							</tr>
							<tr>
								<td class="text2-td1">站点描述：</td>
								<td><input type="text" name="site_description" /></td>
							</tr>
							<!--第二个表单-->
							<tr>
								<th>创始人信息</th>
							</tr>
							<tr>
								<td class="text2-td1">管理员账号：</td>
								<td><input type="text" name="admin_user" /></td>
								<td>管理员账号最少4位</td>
							</tr>
							<tr>
								<td class="text2-td1">管理员密码：</td>
								<td><input type="password" name="admin_pwd"  /></td>
								<td>保证密码最少6位</td>
							</tr>
						</table>
						<!--end-->
					</div>
					<!--按钮-->

					<div class="btn">
						<a class="m195 agree-btn fl" href="<?php echo url('setup1')?>">上一步</a>
						<input type="submit" disabled="disabled" class="agree-btn disabled fl next" value="安装系统" />
					</div>

			</div>
			</form>
		</div>
		<p class="copy">©2016-2017 lzcms.top (LzCMS)</p>
	</body>
<iframe name='install_iframe' style='width:0px;height:0px;display:none' src='#'></iframe>
</body>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type='text/javascript'>
//加载完之后
	$(function(){
		var $val=$("input[type=text]").first().val();
		$("input[type=text]").first().focus().val($val);
		$('[name="db_name"]').focus();
		$('input').on('blur',function(){
			$(this).removeAttr('style');
		})
	})

	var db_info = false;
	//检查表单信息
	function check_form()
	{
		$('label.error').hide();
		var checkObj   =
		{
			db_pre    :/^\w+$/i,
			db_name   :/^(?![^a-zA-Z]+$).{1,}$/i,
			site_name :/^.+$/i,
			admin_user:/.{4,12}/i,
			admin_pwd:/.{6,12}/i
		};

		for(val in checkObj)
		{
			var matchResult = $.trim($('[name="'+val+'"]').val()).match(checkObj[val]);
			if(matchResult == null){
				$('[name="'+val+'"]').css({'border':'1px solid #f00'}).focus();
				$('#'+val+'_label').show();
				return false;
			}
		}
		if(db_info){
			$('.next').attr('disabled','');
		}
		return true;
	}

	//检查mysql链接
	function check_mysql()
	{
		//获取ajax检查mysql链接的所需数据\w+_$
		matchResult = $.trim($('[name="db_pre"]').val()).match(/^\w+_$/);
		if(matchResult == null){
			$('[name="db_pre"]').css({'border':'1px solid #f00'}).focus();
			return false;
		}
		//检查数据库名称
		matchResult = $.trim($('[name="db_name"]').val()).match(/^(?![^a-zA-Z]+$).{1,}$/);
		if(matchResult == null){
			$('[name="db_name"]').css({'border':'1px solid #f00'}).focus();
			return false;
		}
		var sendData = {'db_address':'','db_port':'','db_user':'','db_pwd':'','db_name':''};
		for(val in sendData)
		{
			sendData[val] = $('[name="'+val+'"]').val();
		}

		$.getJSON("<?php echo url('ajax_check_mysql');?>",sendData,function(content)
		{
			var dis_inputs = {'db_address':'','db_port':'','db_user':'','db_pwd':'','db_name':'','db_pre':''};
			if(content.status == 1)
			{
				for(val in dis_inputs){
					$('[name="'+val+'"]').attr('readonly','readonly');
				}
				if(content.sql_mode == 2){
					$('#error_p').show().text("当前数据库为严格模式，请更改为非严格模式再安装");
					return false;
				}else if(content.sql_mode == 1){
					db_info = true;
					$('.checkmysql').removeAttr('onclick').removeClass('agree-btn2').addClass('db_disabled');
					$('.next').removeAttr('disabled','').removeClass('disabled');
					$('#error_p').hide();
					$('#right_p').show().text(content.info);
					//检测错误
					$('[name="site_name"]').focus();
				}
			}else{
				for(val in dis_inputs){
					$('[name="'+val+'"]').removeAttr('disabled');
				}
				$('#right_p').hide();
				$('#error_p').show().text(content.info);
				$('[name="db_user"]').focus();
			}
		});
	}
</script>
</html>
