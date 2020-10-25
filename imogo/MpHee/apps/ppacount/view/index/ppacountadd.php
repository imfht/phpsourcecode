<h2>添加公众账号：</h2>
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
						<option selected value="wechat">微信公众平台</option>
						<option value="qywechat">微信企业号</option>
						<option value="fuwuc">支付宝服务窗</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>公众账号名称：</th>
				<td><input class="input ruler" type="text" name="name" datatype="s1-30" nullmsg="请输入名称！" errormsg="名称至少1个字符,最多30个字符！">
				<span>必须填写</span>
				</td>
			</tr>
			<tr>
				<th>百度地图AK：</th>
				<td><input class="input w400" type="text" name="baidumapak">后面选坐标的应用可能会用到</td>
			</tr>
			<tr>
				<th>二维码：</th>
				<td>
					<div class="button t-imgupload" dataname="qrcode" data="">选择图片</div>
				</td>
			</tr>
			<tr>
				<th>Logo：</th>
				<td>
					<div class="button t-imgupload" dataname="logo" data="">选择图片</div>
				</td>
			</tr>
			<tr>
				<th>账号描述：</th>
				<td>
					<textarea id="description" class="t-editor" name="description"></textarea>
				</td>
			</tr>
			<tr>
				<th>公众账号状态：</th>
				<td>
					<select name="state">
						<option selected value="1">启用</option>
						<option value="0">停用</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div id="con_one_2" class="form_box" style="display:none">
		<div class="info m10 p10">没有请留空，勿乱填写</div>
		<table>
			<tr>
				<th>AppID：</th>
				<td><input class="input w400" type="text" name="appid"></td>
			</tr>
			<tr>
				<th>AppSecret：</th>
				<td><input class="input w400" type="text" name="appsecret"></td>
			</tr>
			<tr>
				<th>EncodingAesKey：</th>
				<td><input class="input w400" type="text" name="encodingaeskey"></td>
			</tr>
		</table>
	</div>
	<div class="btn">
		<input class="button" value="确定" type="submit">
        <div class="button" onclick="history.go(-1);">取消</div>
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