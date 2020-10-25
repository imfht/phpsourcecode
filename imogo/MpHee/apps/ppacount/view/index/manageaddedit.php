<h2>添加管理账户：</h2>
<hr class="mb10"></hr>

<form class="t-form" enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="">
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>功能管理：</th>
				<td>
					<table id="actiontab">
						<tr style="background:#eceff2">
							<td width="100">账号名称</td>
							<td>管理功能</td>
							<td width="150">管理</td>
						</tr>
						<!--tr></tr-->
					</table>
					<div class="button" onclick="addaction();">添加管理</div>
					<input type="hidden" name="manage" value="{$manageinfo['manage']}">
				</td>
			</tr>
			{if empty($manageinfo['username'])}
			<tr>
				<th>管理账户用户名：</th>
				<td>
					<input class="w200" type="text" name="username" value="{$manageinfo['username']}" ajaxurl="{url('index/validusername')}" datatype="s1-30" nullmsg="请输入用户名！" errormsg="名称至少1个字符,最多30个字符！"/><span>必须填写</span><br>例如：填写的是test，默认加前缀{$userinfo['username']}_，变成{$userinfo['username']}_test用户名
				</td>
			</tr>
			{/if}
            <tr>
				<th>管理账户密码：</th>
				<td>
					<input class="w200" type="text" name="password" value=""/>留空则不修改密码
				</td>
			</tr>
			<tr>
				<th>备注：</th>
				<td>
					<textarea id="editor" name="remark" class="w400 h150">{$manageinfo['remark']}</textarea>
				</td>
			</tr>
		</table>
	</div>
	<div class="btn">
		<input onclick="saveaction();" class="button" value="确定" type="submit">
        <input onclick="history.go(-1);" class="button" value="取消" type="reset">
	</div>
</form>
<script type="text/javascript">
function saveaction(){
	var actiontr = $('#actiontab').find( 'tr' );
	if( actiontr.length > 1 ){
	var data = '[';
	for(var i = 1;i<actiontr.length;i++){
		data += '{"ppid":' + actiontr.eq(i).find( 'td' ).eq(0).attr('data') + ',"action":[';
		var span = actiontr.eq(i).find( 'td' ).eq(1).find( 'span' );
		
		for(var j = 0;j<span.length;j++){
			data += '"' + span.eq(j).attr('data') + '",';
		}
		data = data.slice(0,-1);
		data += ']},';
	}
	data = data.slice(0,-1);
	data += ']';
	$('input[name=manage]').val( data );
	}
}

function editaction(obj){
	var actiontr = $(obj).parent().parent();
	var ppid = actiontr.find( 'td' ).eq(0).attr('data');
	var actions = '';
	var span = actiontr.find( 'td' ).eq(1).find( 'span' );
	for(var i = 0;i<span.length;i++){
		actions += '' + span.eq(i).attr('data') + '_';
	}
	actions = actions.slice(0,-1);
	
	$.layer({
    type: 2,
    border: [0],
    title: false,
    shadeClose: true,
    closeBtn: false,
    iframe: {src : "{url('index/actionedit')}"+"&actions="+actions+"&ppid="+ppid},
    area: ['860px', '400px']
	});
}

function addaction(){
var ppacountcount = <?php echo count($pplist);?>;
var actiontr = $('#actiontab').find( 'tr' );
var data = '';
if( actiontr.length <= ppacountcount){
for(var i = 1;i<actiontr.length;i++){
	data += actiontr.eq(i).find( 'td' ).eq(0).attr('data') + '_';
}
data = data.slice(0,-1);

$.layer({
    type: 2,
    border: [0],
    title: false,
    shadeClose: true,
    closeBtn: false,
    iframe: {src : "{url('index/actionadd')}"+"&ppid="+data},
    area: ['860px', '400px']
});
}else{
	alert("已经没有帐号可以添加！");
}
}

function ppname(ppid){
	var pplist = <?php echo json_encode($pplist);?>;
	for(var i = 0;i < pplist.length;i++){
		if( pplist[i].id == ppid){
			return pplist[i].name;
		}
	}
}

function actionname(action){
	var actions = <?php echo json_encode($apps);?>;
	return actions[action].APP_NAME;
}

Do.ready('base','layer','form', function(){
$(".t-form").Form({});

var all = JSON.parse( '{$manageinfo["manage"]}' );

for(var i = 0;i<all.length;i++){
	var text = '<tr id="tr'+all[i].ppid+'"><td data="'+all[i].ppid+'">'+ppname( all[i].ppid )+'</td><td>';
	var actions = all[i].action;
	for(var j = 0;j<actions.length;j++){
		text += '<span class="mr10 mb5" data="'+actions[j]+'">'+actionname( actions[j] )+'<i onclick="$(this).parent().remove();"> X</i></span>';
	}
	text += '</td><td><div onclick="editaction(this);" class="button"><i class="fa fa-edit fa-lg"></i> 修改</div><div onclick="$(this).parent().parent().remove();" class="button"><i class="fa fa-trash-o fa-lg"></i> 删除</div></td></tr>';
	$('#actiontab').append(text);
}

});
</script>