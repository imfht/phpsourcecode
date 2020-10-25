<?php
$checkObj = new checkConfig();
?>
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
						<span class="step-num  bg-img s2_off fl"></span>
						<span class="step-num  bg-img s3_off fl"></span>
					</div>
				</div>
					<div class="clear"></div>
					
					<!--安装内容-->
					<div class="Test">
						<table>
							<tr>
							    <th>环境检测</th>
							    <th>推荐配置</th>
							    <th>当前状态</th>
							    <th>最低要求</th>
							</tr>
							<tr>
							    <td>操作系统</td>
							    <td>类UNIX</td>
							    <td><?php $os = explode(" ", php_uname()); echo $os[0];?></td>
							    <td>--</td>
							</tr>
							<tr>
							    <td>PHP版本</td>
							    <td>>5.4.x</td>
							    <td>
								<?php //phpversion检查
								$phpVersion_pass = $checkObj->c_phpVersion();?>
								<?php echo phpversion();?>
								<?php echo $phpVersion_pass ? '<em class="bg-img ok" />' : '<em class="bg-img no" />';?>
								</td>
							    <td>5.4.x</td>
							</tr>
							<tr><td colspan="4" style="border-bottom: 1px solid #c0c0c0;height: 1px;"></td></tr>  
							<?php //phpini检查
							$phpiniArray = $checkObj->c_phpIni();
							foreach($phpiniArray as $key => $val):
							?>
							<tr>
								<td><?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?></td>
								<td>基础配置</td>
								<td>
								<?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?>
								<?php echo $val ? '<em class="bg-img ok" />' : '<em class="bg-img no" />';?>
								</td>
								<td>启用</td>
							</tr>
							<?php endforeach;?>
							<tr><td colspan="4" style="border-bottom: 1px solid #c0c0c0;height: 1px;"></td></tr>
							<?php //must_extension检查
							$mustExtensionArray = $checkObj->c_must_extension();
							foreach($mustExtensionArray as $key => $val):
							?>
							<tr>
								<td><?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?></td>
								<td>必须启用</td>
								<td>
								<?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?>
								<?php echo $val ? '<em class="bg-img ok" />' : '<em class="bg-img no" />';?>
								</td>
								<td>打开</td>
							</tr>
							<?php endforeach;?>
							<tr><td colspan="4" style="border-bottom: 1px solid #c0c0c0;height: 1px;"></td></tr>
							<?php //recom_extension检查
							$recomExtensionArray = $checkObj->c_recom_extension();
							foreach($recomExtensionArray as $key => $val):
							?>
							<tr>
								<td><?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?></td>
								<td>建议扩展</td>
								<td>
								<?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?>
								<?php echo $val ? '<em class="bg-img ok" />' : '<em class="bg-img no" />';?>
								</td>
								<td>--</td>
							</tr>
							<?php endforeach;?>
							<tr><td colspan="4" style="border-bottom: 1px solid #c0c0c0;height: 1px;"></td></tr>
							<?php //writeable
							$writeableArray = $checkObj->c_writeableDir();
							foreach($writeableArray as $key => $val):
							?>
							<tr>
								<td><?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?></td>
								<td>必须可写</td>
								<td>
								<?php echo $key;?><?php if(!$val){?><?php echo configInfo($key);?><?php }?>
								<?php echo $val ? '<em class="bg-img ok" />' : '<em class="bg-img no" />';?>
								</td>
								<td>可写</td>
							</tr>
							<?php endforeach;?>
							
							 
							
							</table>
					</div>
					<!--按钮-->

					<div class="btn">
						<a class="m195 agree-btn fl" onclick="window.location.reload(true);">重新检测环境</a>
						<a class="agree-btn disabled fl" id="_right_" onclick="check_config();">下一步</a>
					</div>
			</div>
		</div>
		<p class="copy">©2016-2017 lzcms.top (LzCMS)</p>
	</body>
	<script type='text/javascript'>
	ErrorNum = <?php echo $checkObj->getNpassMustNum();?>

	//检查配置信息
	function check_config()
	{
		var error_num = ErrorNum;
		if(error_num > 0)
		{
			alert('您的系统环境配置没有通过检查');
		}
		else
		{
			window.location.href = '<?php echo url('setup2')?>';
		}
	}

	if(ErrorNum > 0)
	{
	}
	else
	{
		document.getElementById('_right_').className = 'agree-btn fl';
	}
</script>
</html>
