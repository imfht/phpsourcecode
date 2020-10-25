<h2>添加管理行为：</h2>
<hr class="mb10"></hr>

<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>功能管理：</th>
				<td>
					{loop $pplist $vo}
						<label id="ppid{$vo['id']}"><input name="ppid" type="radio" value="{$vo['id']}" /><span>{$vo['name']}</span></label>
					{/loop}
				</td>
			</tr>
			<tr>
				<th>管理功能：</th>
				<td>
					{loop $apps $app $config}
						<label><input name="actions" type="checkbox" value="{$app}" /><span>{$config['APP_NAME']}</span></label>
					{/loop}
				</td>
			</tr>
		</table>
</div>
<div class="btn">
	<div class="button" id="okbtn">确定</div>
    <div class="button" id="closebtn">取消</div>
</div>
<script type="text/javascript">
Do.ready('base','layer', function(){
var ppid = {$okppid};
for(var i = 0;i<ppid.length;i++){
	$('#ppid'+ppid[i]).attr('style','display:none');
}

var index = parent.layer.getFrameIndex(window.name); //获取当前窗体索引

$('#closebtn').on('click', function(){
    parent.layer.close(index); //执行关闭
});

$('#okbtn').on('click', function(){
    var ppid = $(':radio[name="ppid"]:checked').val();
	var ppname = $(':radio[name="ppid"]:checked + span').text();
	
	var text='<tr id="tr'+ppid+'"><td data="'+ppid+'">'+ppname+'</td><td>';  
    $("[name='actions']:checked").each(function() {  
        text += '<span class="mr10 mb5" data="'+$(this).val()+'">'+$(this).siblings('span').text()+'<i onclick="$(this).parent().remove();"> X</i></span>'; 
    });
	text += '</td><td><div onclick="editaction(this);" class="button"><i class="fa fa-edit fa-lg"></i> 修改</div><div onclick="$(this).parent().parent().remove();" class="button"><i class="fa fa-trash-o fa-lg"></i> 删除</div></td></tr>';
	
	parent.$('#actiontab').append(text);
    parent.layer.close(index);
});

});
</script>