<h2>添加用户分组：</h2>
<hr class="mb10"></hr>

<form enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="">
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>分组名称：</th>
				<td><input class="input ruler" value="{$groupinfo['name']}" type="text" name="name">必须填写</td>
			</tr>
			<tr>
				<th>分组排序：</th>
				<td>
					<input class="input w200" value="{$groupinfo['sort']}" type="text" name="sort">例如：0,1,2,3不填写默认为0
				</td>
			</tr>
			<tr>
				<th>简介：</th>
				<td>
					<textarea class="textarea w400 h80" id="editor" name="description">{$groupinfo['description']}</textarea>
				</td>
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

Do.ready(function(){

});
</script>