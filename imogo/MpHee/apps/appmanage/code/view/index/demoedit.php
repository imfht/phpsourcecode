<h2>修改数据：</h2>
<hr class="mb10"></hr>

<form enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="#">
	<ul class="tab">
		<li id="one1" class="hover" onclick="setTab('one',1,2)">基本信息</li>
		<li id="one2" onclick="setTab('one',2,2)">附加设置</li>
	</ul>
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>分类：</th>
				<td>
					<select name="cid">
						<option selected value="">选择分类</option>
						<option value="1">分类一</option>
						<option value="2">分类二</option>
					</select>
				</td>
			</tr>
            <tr>
				<th>属性：</th>
				<td>
					<select id="recommend" name="recommend">
						<option selected value="1">属性一</option>
						<option style="color: #090" value="2">属性二</option>
						<option style="color: #f63" value="3">属性三</option>
					</select>
					
					<select id="top" name="top">
						<option selected value="2">顶部</option>
						<option style="color: #f63" value="1">底部</option>
					</select>
					
					<select id="state" name="state">
						<option selected value="1">显示</option>
						<option style="color: #f63" value="2">不显示</option>
					</select>
					常规属性,如果不是特殊内容可以不用设置</td>
			</tr>
			<tr>
				<th>标题：</th>
				<td><input class="input ruler" type="text" value="{$demoinfo['title']}" name="title">必须填写</td>
			</tr>
			<tr>
				<th>缩图：</th>
				<td>
					<input id="thumb" class="input w200" type="text" name="thumb">
					<a class="button" onclick="#" href="javascript:void(0);">上传缩图</a>
				</td>
			</tr>
			<tr>
				<th>简单描述： </th>
				<td>
					<textarea class="textarea w400 h80" onkeyup=value=value.substr(0,110); name="description"></textarea>
                长度不能超过110个字符，留空将自动提取文章前110个字符 </td>
            </tr>
			<tr>
				<th>内容：</th>
				<td>
					<textarea id="editor" name="content"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<div id="con_one_2" class="form_box" style="display:none">
		<table width="100%">
			<tr>
				<th>关联内容：</th>
				<td>
					<input class="input w400" type="text" name="reid">
					<a class="button" onclick="" href="javascript:void(0);">选择相关</a>
				</td>
			</tr>
            <tr>
				<th>关键字：</th>
				<td><input id="keywords" class="input w400" type="text" name="keywords">请使用全角逗号分隔。</td>
            </tr>
            <tr>
				<th>开始时间：</th>
				<td colspan="3"><input placeholder="请输入开始日期" value="" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" class="laydate-icon" name="starttime"></td>
            </tr>
            <tr>
				<th>结束时间：</th>
				<td colspan="3"><input placeholder="请输入结束日期" value="" onclick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" class="laydate-icon" name="endtime"></td>
            </tr>
			<tr>
				<th>创建时间：</th>
				<td colspan="3"><input placeholder="请输入结束日期" value="{$demoinfo['createtime']}" class="laydate-icon" name="endtime"></td>
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

Do.ready('base','kindeditor','laydate','upload', function(){
KindEditor.ready(function(K) {
	K.create('#editor', {
		width: '720px',
		height: '460px',
		allowFileManager : true
	});
});
});
</script>