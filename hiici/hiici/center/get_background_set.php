<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

$user_info = dt_query_one("SELECT background_url FROM user_info WHERE id = ".$auth['id']);
if (!$user_info) die('获取数据失败！');


?>
<div class="col-md-12 column">
	<div class="well center-well">
		<form class="form-horizontal" id="background_set">
			<legend><span class="glyphicon glyphicon-edit"></span> 新背景URL</legend>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<input class="form-control" id="background" name="background" value="<?php echo $user_info['background_url'] ?>"/>
				</div>
				<label class="control-label">背景URL</label>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_background_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 保存背景</a>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#background_set').addClass('active');
});
function do_background_set()
{
	$.get('?c=center&a=do_background_set&background='+$('input#background').val(), function (rs) { 
		if (rs != 's0') {
			alert(rs);
			return;
		}
		alert ('成功保存了！^_^');
	});
}

</script>

