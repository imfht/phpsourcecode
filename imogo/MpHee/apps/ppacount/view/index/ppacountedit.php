<h2>修改公众账号：</h2>
<hr class="mb10"></hr>

<form class="t-form" enctype="multipart/form-data" method="post" action="">
	<ul class="tab">
		<li id="one1" class="hover" onclick="setTab('one',1,3)">基本信息</li>
		<li id="one2" onclick="setTab('one',2,3)">接口参数</li>
	</ul>
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>公众账号类型：</th>
				<td>
					<select name="category">
						<option value="wechat">微信公众平台</option>
						<option value="wechatqy">微信企业号</option>
						<option value="fuwuc">支付宝服务窗</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>公众账号名称：</th>
				<td><input class="input ruler" type="text" name="name" value="{$ppacountinfo['name']}" datatype="s1-30" nullmsg="请输入名称！" errormsg="名称至少1个字符,最多30个字符！">
				<span>必须填写</span>
				</td>
			</tr>
			<tr>
				<th>百度地图AK：</th>
				<td><input class="input w400" type="text" value="{$ppacountinfo['baidumapak']}" name="baidumapak">后面选坐标的应用可能会用到</td>
			</tr>
			<tr>
				<th>二维码：</th>
				<td>
					<div class="button t-imgupload" dataname="qrcode" data="{$ppacountinfo['qrcode']}">选择图片</div>
				</td>
			</tr>
			<tr>
				<th>Logo：</th>
				<td>
					<div class="button t-imgupload" dataname="logo" data="{$ppacountinfo['logo']}">选择图片</div>
				</td>
			</tr>
			<tr>
				<th>内容：</th>
				<td>
					<textarea id="description" class="t-editor" name="description">{$ppacountinfo['description']}</textarea>
				</td>
			</tr>
		</table>
	</div>
	<div id="con_one_2" class="form_box" style="display:none">
		<div class="info m10 p10">没有请留空，勿乱填写</div>
		<table>
			<tr>
				<th>AppID：</th>
				<td><input class="input w400" type="text" name="appid" value="{$ppacountinfo['appid']}"></td>
			</tr>
			<tr>
				<th>AppSecret：</th>
				<td><input class="input w400" type="text" name="appsecret" value="{$ppacountinfo['appsecret']}"></td>
			</tr>
			<tr>
				<th>EncodingAesKey：</th>
				<td><input class="input w400" type="text" name="encodingaeskey" value="{$ppacountinfo['encodingaeskey']}"></td>
			</tr>
		</table>
	</div>
	<div class="btn">
		<input class="button" value="确定" type="submit">
        <input class="button" value="重置" type="reset">
	</div>
</form>
<script type="text/javascript">
function setTab(id, num, n){
	for(var i=1; i<=n; i++){
		$("#"+id+i).removeClass('hover');
		$("#con_"+id+"_"+i).attr('style','display:none');
	}
	$("#"+id+num).addClass('hover');
	$("#con_"+id+"_"+num).removeAttr('style');
}

Do.ready('base','form','upload','kindeditor', function(){
$(".t-imgupload").FileUpload({});
$(".t-editor").Editor({});
$(".t-form").Form({});
});
</script>