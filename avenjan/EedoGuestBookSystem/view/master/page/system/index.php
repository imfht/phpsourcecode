<?php
 include $_SERVER['DOCUMENT_ROOT'].'/libs/function.php';
 session();//权限控制
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>系统基本参数设置</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/src/layui/css/layui.css" media="all" />
	<style type="text/css">
		.layui-table td, .layui-table th{ text-align: center; }
		.layui-table td{ padding:5px; }
	</style>
</head>
<body class="childrenBody">
	<form class="layui-form" id="editchickform">
		<table class="layui-table">
			<colgroup>
				<col width="20%">
				<col width="45%">
				<col>
		    </colgroup>
		    <thead>
		    	<tr>
		    		<th>参数说明</th>
		    		<th>参数值</th>
		    		<th>变量名</th>
		    		<th>说明</th>
		    	</tr>
		    </thead>
		    <tbody>
		    	<tr>
		    		<td>系统名称</td>
		    		<td><input type="text" name="sitename" class="layui-input cmsName" lay-verify="required" placeholder="请输入系统名称" value="<?php echo $system_sitename;?>"></td>
		    		<td>sitename</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>网站首页</td>
		    		<td><input type="text" name="domain" class="layui-input version" lay-verify="required" placeholder="请输入网站首页" value="<?php echo $system_domain;?>"></td>
		    		<td>domain</td>
		    		<td>例如：http://www.eedo.net/</td>
		    	</tr>
		    	<tr>
		    		<td>系统版本</td>
		    		<td><input type="text" name="version" class="layui-input author"  placeholder="请输入系统版本" value="<?php echo $system_version;?>"></td>
		    		<td>version</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>站点关键字</td>
		    		<td><input type="text" name="keywords" class="layui-input author" placeholder="请输入站点关键字" value="<?php echo $system_keywords;?>"></td>
		    		<td>keywords</td>
		    		<td>使用逗号分隔</td>
		    	</tr>
		    	<tr>
		    		<td>站点描述</td>
		    		<td>
 					<textarea placeholder="请输入站点描述" name="descript" class="layui-textarea linksDesc" lay-verify=""><?php echo $system_descript;?></textarea>
		    		</td>
		    		<td>descript</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>网站备案号</td>
		    		<td><input type="text" name="icp" class="layui-input record" placeholder="请输入网站备案号" value="<?php echo $system_icp;?>"></td>
		    		<td>icp</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>logo网址</td>
		    		<td><input type="text" name="logourl" class="layui-input record" placeholder="请输入logo url" value="<?php echo $system_logourl;?>"></td>
		    		<td>logourl</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>短信通知</td>
		    		<td style="text-align: left;">
		    			<input type="checkbox" name="sendsms" lay-skin="switch" lay-text="开启|关闭" <?php echo ($system_sendsms =='on') ? "checked" : "" ?>>
		    		</td>
		    		<td>sendsms</td>
		    		<td>是否开启新留言发布后短信通知管理员</td>
		    	</tr>
		    	<tr>
		    		<td>KeyId</td>
		    		<td><input type="text" name="KeyId" class="layui-input record" placeholder="KeyId" value="<?php echo $system_KeyId;?>"></td>
		    		<td>KeyId</td>
		    		<td>阿里云通信的accessKeyId</td>
		    	</tr>
		    	<tr>
		    		<td>KeySecret</td>
		    		<td><input type="text" name="KeySecret" class="layui-input record" placeholder="KeySecret" value="<?php echo $system_KeySecret;?>"></td>
		    		<td>KeySecret</td>
		    		<td>阿里云通信的accessKeySecret</td>
		    	</tr>
		    	<tr>
		    		<td>短信签名</td>
		    		<td><input type="text" name="SignName" class="layui-input record" placeholder="SignName" value="<?php echo $system_SignName;?>"></td>
		    		<td>SignName</td>
		    		<td>阿里云通信的短信签名</td>
		    	</tr>
		    	<tr>
		    		<td>模版CODE</td>
		    		<td><input type="text" name="TemplateCode" class="layui-input record" placeholder="TemplateCode" value="<?php echo $system_TemplateCode;?>"></td>
		    		<td>TemplateCode</td>
		    		<td>阿里云通信的模版CODE</td>
		    	</tr>
		    	<tr>
		    		<td>短信发送留言内容</td>
		    		<td style="text-align: left;">
		    			<input type="checkbox" name="sendcontent" lay-skin="switch" lay-text="是|否" <?php echo ($system_sendcontent =='on') ? "checked" : "" ?>>
		    		</td>
		    		<td>sendcontent</td>
		    		<td>短信是否包含留言内容</td>
		    	</tr>
		    	<tr>
		    		<td>短信通知号码</td>
		    		<td><input type="text" name="smsnumber" class="layui-input record" placeholder="请输入短信通知号码" value="<?php echo $system_smsnumber;?>"></td>
		    		<td>smsnumber</td>
		    		<td>接收短信通知的号码</td>
		    	</tr>
		    	<tr>
		    		<td>版权信息</td>
		    		<td><input type="text" name="copyright" class="layui-input record" placeholder="版权信息" value="<?php echo $system_copyright;?>"></td>
		    		<td>copyright</td>
		    		<td>页脚版权信息</td>
		    	</tr>
		    	<tr>
		    		<td>其他代码</td>
		    		<td><textarea placeholder="请输入" name="footercode" class="layui-textarea linksDesc" lay-verify=""><?php echo $system_footercode;?></textarea></td>
		    		<td>footercode</td>
		    		<td>页脚显示，可放置统计代码，广告代码，友情链接代码等内容</td>
		    	</tr>
		    	<tr>
		    		<td>显示留言位置</td>
		    		<td style="text-align: left;">
		    			<input type="checkbox" name="viewcity" lay-skin="switch" lay-text="是|否" <?php echo ($system_viewcity =='on') ? "checked" : "" ?>>
		    		</td>
		    		<td>viewcity</td>
		    		<td>是否显示留言访客位置信息，测试环境请勿开启！</td>
		    	</tr>
		    </tbody>
		</table>
		<div class="layui-form-item" style="text-align:center;">
			<div class="layui-input-block">
				<a class="layui-btn" lay-submit="" lay-filter="editchick">立即提交</a>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
	<blockquote class="layui-elem-quote">说明：<br>
		前台调用方法：<br>
		例如调用站点名称：<br>
		echo $system_sitename;<br>
		即可
	</blockquote>
	<script type="text/javascript" src="/src/layui/layui.js"></script>
	<script type="text/javascript" src="system.js"></script>
</body>
</html>