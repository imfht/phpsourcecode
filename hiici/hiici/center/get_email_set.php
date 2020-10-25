<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

$user = dt_query_one("SELECT email FROM user WHERE id = ".$auth['id']);
if (!$user) die('获取数据失败！');

?>
<div class="col-md-12 column">
	<div class="well center-well">
		<form class="form-horizontal" id="email_set">
			<legend><span class="glyphicon glyphicon-edit"></span> Email设置</legend>
			<br>
			<div class="row clearfix">
				<div class="col-md-8">
					<input class="form-control" id="email" name="email" value="<?php echo $user['email'] ?>" required=""/>
				</div>
				<label class="control-label">Email (用于找回密码)</label>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-6">
					<input id="email_code" name="email_code" type="text" placeholder="邮箱激活码" class="form-control input-md" required="">
				</div>
				<div class="col-md-2">
					<a class="btn btn-default btn-block" type="button" href="javascript:get_email_code()">获取激活码</a>
				</div>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_email_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 保存Email</a>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#email_set').addClass('active');
});
function do_email_set()
{
	$.get('?c=center&a=do_email_set&email='+$('input#email').val()+'&email_code='+$('input#email_code').val(), function (rs) { 
		if (rs != 's0') {
			alert(rs);
			return;
		}
		alert ('成功保存了！^_^');
		$('input#email_code').val('');
	});
}

</script>

