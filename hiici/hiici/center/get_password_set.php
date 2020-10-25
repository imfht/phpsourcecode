<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

?>
<div class="col-md-12 column">
	<div class="well center-well">
		<form class="form-horizontal" id="password_set">
			<legend><span class="glyphicon glyphicon-edit"></span> 新密码</legend>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<input type="password" class="form-control" id="password_old" name="password_old"/>
				</div>
				<label class="control-label">旧密码</label>
			</div>
			<hr>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<input type="password" class="form-control" id="password_new" name="password_new"/>
				</div>
				<label class="control-label">新密码</label>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<input type="password" class="form-control" id="password_new_r" name="password_new_r"/>
				</div>
				<label class="control-label">重复新密码</label>
			</div>
			<hr>
			<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_password_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 保存密码</a>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#password_set').addClass('active');
});
function do_password_set()
{
	$.post('?c=center&a=do_password_set', {password_old: $('input#password_old').val(), password_new: $('input#password_new').val(), password_new_r: $('input#password_new_r').val(), token: $('input[name=token]').val()}, function (rs) {
		if (rs != 's0') {
			rs = $.parseJSON(rs);
			$("form#password_set").find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert ('成功保存了！^_^');
		location = '?c=user&a=do_logout';
	});
}

</script>

