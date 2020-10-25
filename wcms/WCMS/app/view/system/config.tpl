{include file="news/header.tpl"}

<!-- 头部// -->
{include file="news/top.tpl"}


{include file="news/nav.tpl"}
	<!-- start: Content -->
			<div style="margin:10px;">
			
						
			<div class="row-fluid">







<table class="table table-striped table-condensed span5 pull-left">

	<form class="form" style="margin:0px;">
<tr><th class="span3">基础</th><th></th></tr>

     <tr>
		<td  class="span2" >域名</td>
		<td  class="span10" ><input type="text" name="website" value="{$config.website}"></td>

	</tr>
	 <tr>
		<td  class="span2" >版权</td>
		<td  class="span10" ><input type="text" name="copyright" value="{$config.copyright}"></td>

	</tr>
	
	
	 <tr>
		<td  class="span2" >QQ登录APPID</td>
		<td  class="span10" ><input type="text" name="qq" value="{$config.qq}"></td>

	</tr>
	
	<tr>
		<td  class="span2" >日志保留天数</td>
		<td  class="span10" ><input type="text" name="log"  class="input-small" value="{$config.log}">天</td>

	</tr>
	<tr>
		<td  class="span2" >新闻显示条数</td>
		<td  class="span10" ><input type="text" name="pagenum" value="{$config.pagenum}"></td>

	</tr>
	
	<tr>
		<td  class="span2" >通知分类</td>
		<td  class="span10" ><input type="text" name="receipt" value="{$config.receipt}"></td>

	</tr>
	
	
	<tr>
		<td  class="span2" >头像审核</td>
<td ><input type="radio" name="face" value="1"
		{if $config.face==1}checked="checked"{/if}
		>开启 <input type="radio" name="face" value="0"
				{if $config.face==0}checked="checked"{/if}
		
		
		>关闭 </td>
	</tr>
	
	
	<tr>
		<td  class="span2" >二维码尺寸</td>
		<td  class="span10" ><input type="text" name="ewm_size" value="{$config.ewm_size}"></td>

	</tr>
	
	
	
	
	<tr>
		<td  class="span2" >二维码Logo</td>
<td ><input type="radio" name="islogo" value="1"
		{if $config.islogo==1}checked="checked"{/if}
		>开启 <input type="radio" name="islogo" value="0"
				{if $config.islogo==0}checked="checked"{/if}
		
		
		>关闭 </td>
	</tr>
	

	<tr>
		<td >统计缓存</td>
		<td ><input type="radio" name="cache_num" value="1"
		{if $config.cache_num==1}checked="checked"{/if}
		>开启 <input type="radio" name="cache_num" value="0"
				{if $config.cache_num==0}checked="checked"{/if}
		
		
		>关闭 </td>

	</tr>

	<tr><th>水印配置</th><th></th></tr>

	<tr>
		<td >水印</td>
		<td ><input type="radio" name="water" value="1"
		{if $config.water==1}checked="checked"{/if}
		>开启 <input type="radio" name="water" value="0"
				{if $config.water==0}checked="checked"{/if}
		
		
		>关闭 </td>

	</tr>
	
	<tr>
		<td >水印类型</td>
		<td ><select name="water_type" class="input-middle">
		<option value="1" 		{if $config.water_type==1}selected{/if}
		>文字水印</option>
		<option value="2"
			{if $config.water_type==2}selected{/if}
		>图片水印</option>
		</select></td>

	</tr>
	
	<tr>
		<td  class="span2" >文字水印</td>
		<td  class="span10" ><input type="text" name="water_text" value="{$config.water_text}"></td>

	</tr>
	
	<tr>
		<td  class="span2" >图片水印</td>
		<td  class="span10" >
		<div style="display:none;">
		<input type="file" name="waterfile">
		</div>
		 <a href="javascript:water()" style="color:blue;">上传</a>  png格式</td>

	</tr>
	
	



   <tr>
		<td  class="span2" >文字水印大小</td>
		<td  class="span10" ><input type="text" name="water_fontsize" value="{$config.water_fontsize}"></td>

	</tr>
	<tr>
		<td >水印位置</td>
		<td ><select name="water_position" class="input-small">
		<option value="1" 		{if $config.water_position==1}selected{/if}
		>正中间</option>
		<option value="2"
			{if $config.water_position==2}selected{/if}
		>右下角</option>
		<option value="3"
			{if $config.water_position==3}selected{/if}
		>左上角</option>
		</select></td>

	</tr>
	
	<tr>
		<td  class="span2" >图片最大宽度</td>
		<td  class="span10" ><input type="text" name="image_maxwidth" class="input-small" value="{$config.image_maxwidth}">x<input type="text" name="image_maxheight"  class="input-small" value="{$config.image_maxheight}"></td>

	</tr>
	
	<tr>
	<td></td>
	<td><input type="button" value="保存" class="btn"  onclick="save()"></td>
	</tr>
	

</table>
<table class="table table-striped table-condensed span5 pull-left">
	<tr><th>服务器</th><th></th></tr>
	
	<tr>
		<td class="span2" >{$lang['sys']['sys']}</td>
		<td class="span10">{$system.sys}</td>
	</tr>
	<tr>
		<td >{$lang['sys']['php']}</td>
		<td >{$system.phpversion}</td>
	</tr>
		<tr>
		<td >{$lang['sys']['mysql']}</td>
		<td >{$system.mysql} <small>{$system.mysqlsize}</small></td>
	</tr>
		<tr>
		<td >{$lang['sys']['smarty']}</td>
		<td >{$smarty.version}</td>

	</tr>
	
	<tr>
		<td >{$lang['sys']['cgi']}</td>
		<td >{$system.phpapi}</td>
	</tr>
	
	
	<tr>
		<td >{$lang['sys']['timezone']}</td>
		<td >{$zone}</td>
	</tr>
	

</table>

<table class="table table-striped table-condensed span5 pull-left">
	<tr><th>主题颜色</th><th></th></tr>

 <tr>
		<td  class="span3" >顶部颜色</td>
		<td  class="span6" ><input type="text" name="banner_color" class="input-small" value="{$config.banner_color}"></td>

	</tr>
	
	 <tr>
		<td  class="span3" >左侧背景</td>
		<td  class="span6" ><input type="text" name="leftbg_color" class="input-small" value="{$config.leftbg_color}">
		<input type="text" name="left_selected_color"  class="input-small" value="{$config.left_selected_color}" placeholder="选中颜色">
		</td>

	</tr>

</table>



<table class="table table-striped table-condensed span5 pull-left">
	<tr><th>多说配置</th><th></th></tr>

 <tr>
		<td  class="span2" >short_name</td>
		<td  class="span10" ><input type="text" name="short_name" value="{$config.short_name}"></td>

	</tr>
	
	 <tr>
		<td  class="span2" >密钥</td>
		<td  class="span10" ><input type="text" name="duoshuo_secret" value="{$config.duoshuo_secret}"></td>

	</tr>
</table>

<table class="table table-striped table-condensed span5 pull-left">

	<tr><th>邮件配置</th><th></th></tr>
	
	<tr>
		<td  class="span2" >账号</td>
		<td  class="span10" ><input type="text" name="email_account" value="{$config.email_account}"></td>

	</tr>
	
	<tr>
		<td  class="span2" >密码</td>
		<td  class="span10" ><input type="text" name="email_password" value="{$config.email_password}"></td>

	</tr>
	
	<tr>
		<td  class="span2" >SMTP</td>
		<td  class="span10" ><input type="text" name="smtp" value="{$config.smtp}"></td>
	</tr>
	
	<tr>
		<td  class="span2" >SMTP端口</td>
		<td  class="span10" ><input type="text" name="smtp_socket" value="{$config.smtp_socket}"></td>
	</tr>
	
	
	
	<tr>
		<td  class="span2" >测试账号</td>
		<td  class="span10" ><input type="text" name="email_test" value="{$config.email_test}">  <a href="javascript:email()">测试</a></td>
	</tr>

	
	<tr>
	<td></td>
	<td><input type="button" value="保存" class="btn" onclick="save()"></td>
	</tr>

</table>



				<div class="well" >
				

</div></div></div>
</form>






{literal}
<SCRIPt>

function water() {
	// 上传方法
	$.upload({
			// 上传地址
			url: './index.php?factory/upload/', 
			// 文件域名字
			fileName: 'waterfile', 
			// 其他表单数据
			params: {name: 'pxblog'},
			// 上传完成后, 返回json, text
			dataType: 'json',
			// 上传之前回调,return true表示可继续上传
			onSend: function() {
					return true;
			},
			// 上传之后回调
			onComplate: function(data) {
					alert(data.message);
			}
	});
}


function email(){
var email=$("input[name='email_test']").val();
$.post("./index.php?email/test",{email_test:email},function(data){
alert("邮件已发送，请查收 !");
},"json")
}
function save(){

	var data=$("form").serialize();
$.get("./index.php?system/save/?"+data,function(data){
	 alert(data.message);
	},"json")
}


</script>

{/literal}
{include file="news/footer.tpl"}

