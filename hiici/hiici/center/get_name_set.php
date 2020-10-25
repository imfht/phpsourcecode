<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

$user_info = dt_query_one("SELECT name, intro FROM user_info WHERE id = ".$auth['id']);
if (!$user_info) die('获取数据失败！');

?>
<div class="col-md-12 column">
	<div class="well center-well">
		<form class="form-horizontal" id="name_set">
			<legend><span class="glyphicon glyphicon-edit"></span> 修改名字</legend>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
				<input class="form-control" id="new_name" name="new_name" value="<?php echo $user_info['name'] ?>"/>
				</div>
				<label class="control-label">名字</label>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<textarea class="form-control" id="new_intro" name="new_intro" rows="3" value="<?php echo $user_info['intro'] ?>"><?php echo $user_info['intro'] ?></textarea>
				</div>
				<label class="control-label">简介</label>
			</div>
			<hr>
			<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_name_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 保存设置</a>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#name_set').addClass('active');
});
function do_name_set()
{
	$.post('?c=center&a=do_name_set', {new_name:$("input#new_name").val(), new_intro:$("textarea#new_intro").val(), token:$('input[name=token]').val()},function (rs){ 
		if (rs != 's0') {
			rs = $.parseJSON(rs);
			$('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert ('成功保存了！^_^');
		location = '?c=center';
	});
}
</script>

