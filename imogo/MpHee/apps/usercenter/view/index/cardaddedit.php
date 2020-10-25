<h2>添加会员卡：</h2>
<hr class="mb10"></hr>

<form enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="">
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>会员卡名称：</th>
				<td><input class="input w300" value="{$info['cardname']}" type="text" name="cardname">必须填写，例如：XXX旗舰店-金卡</td>
			</tr>
			<tr>
				<th>会员卡名称颜色：</th>
				<td><input class="input w150 t-color" value="{$info['cardnamecolor']}" type="text" name="cardnamecolor"></td>
			</tr>
			<tr>
				<th>会员卡正面背景：</th>
				<td>
					<div class="button t-imgupload" dataname="cardzheng" data="{$info['cardzheng']}">选择正面图片</div>建议图片尺寸512×315，质量5-7
				</td>
			</tr>
			<tr>
				<th>会员卡反面背景：</th>
				<td>
					<div class="button t-imgupload" dataname="cardfan" data="{$info['cardfan']}">选择反面图片</div>建议图片尺寸512×315，质量5-7
				</td>
			</tr>
			<tr>
				<th>正面卡号颜色：</th>
				<td><input class="input w150 t-color" value="{$info['numcolor']}" type="text" name="numcolor"></td>
			</tr>
			<tr>
				<th>正面姓名颜色：</th>
				<td><input class="input w150 t-color" value="{$info['unamecolor']}" type="text" name="unamecolor"></td>
			</tr>
			<tr>
				<th>会员卡界面：</th>
				<td>
					<input value="{$info['cardselect']}" type="hidden" name="cardselect">
					<div class="sortable">
						<!--li class="mt10" id="li1">
							<input id="icon" class="w180" type="text"><input id="name" class="w180" type="text"><input class="w400" id="url" type="text"><i class="fa fa-arrows fa-lg"></i> 排序 <span onclick="$(this).parent().remove();"><i class="fa fa-trash-o fa-lg"></i> 删除</span>
						</li-->
						<div class="button mt10 addli" onclick="addli();">添加一个</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>联系电话：</th>
				<td><input class="input w300" value="{$info['cardphone']}" type="text" name="cardphone"></td>
			</tr>
			<tr>
				<th>联系地址：</th>
				<td><input class="input w300" value="{$info['cardaddress']}" type="text" name="cardaddress"></td>
			</tr>
		</table>
	</div>
	<div class="btn">
		<input onclick="savedata();" class="button" value="确定" type="submit">
        <input class="button" value="重置" type="reset">
	</div>
</form>
<script type="text/javascript">
function addli(){
	var id = randstr(3);
	
	var newli = '<li class="mt10" id="li'+id+'"><input id="icon" class="w180" type="text"><input id="name" class="w180" type="text"><input id="url" class="w400" type="text"><i class="fa fa-arrows fa-lg"></i> 排序 <span onclick="$(this).parent().remove();"><i class="fa fa-trash-o fa-lg"></i> 删除</li></span>';
	$(".addli").before( newli );
	$('.sortable').sortable();
}

function randstr(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function savedata(){
		var data = [];
		$('.sortable').find('li').each(function(){
			var li = $(this);
			var sd = {};
			sd.icon = $.trim(li.find('input[id="icon"]').val());
			sd.name = $.trim(li.find('input[id="name"]').val());
			sd.url = $.trim(li.find('input[id="url"]').val());
			data[data.length] = sd;
		});
		$("input[name='cardselect']").val(JSON.stringify(data));
}

function beselect(selectdata)
{
var select = eval('(' + selectdata + ')');

for(var i = 0;i < select.length; i++ ){
	addli();
	var selectli = $('.sortable').children();
	var nowselectli = $(selectli[i]);
	nowselectli.children('#icon').val(select[i].icon);
	nowselectli.children('#name').val(select[i].name);
	nowselectli.children('#url').val(select[i].url);
}
}

Do.ready('base','form','upload','sort','color', function(){
$('.sortable').sortable();
$(".t-color").Color();
$(".t-imgupload").FileUpload({});

var selectdata = '{$info["cardselect"]}';

beselect(selectdata);

});

</script>