<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

$user_info = dt_query_one("SELECT audio_url FROM user_info WHERE id = ".$auth['id']);
if (!$user_info) die('获取数据失败！');

?>
<div class="col-md-12 column">
	<div class="well center-well">
		<form class="form-horizontal" id="audio_set">
			<legend><span class="glyphicon glyphicon-edit"></span> 空间音乐URL</legend>
			<br>
			<div class="row clearfix">
				<div class="col-md-8 column">
					<input class="form-control" id="audio" name="audio" value="<?php echo $user_info['audio_url'] ?>"/>
				</div>
				<label class="control-label">空间音乐URL</label>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_audio_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 保存音乐</a>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#audio_set').addClass('active');
});
function do_audio_set()
{
	$.get('?c=center&a=do_audio_set&audio='+$('input#audio').val(), function (rs) { 
		if (rs != 's0') {
			alert(rs);
			return;
		}
		alert ('成功保存了！^_^');
	});
}

</script>

