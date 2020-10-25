<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

?>

<div class="col-md-12 column">
	<div class="well center-well">
		<form id="face_set">
			<legend><span class="glyphicon glyphicon-th-large"></span> 当前头像</legend>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<img src="<?php echo FACE_URL.$auth['id'] ?>.jpg"/>
				</div>
			</div>
			<br>
			<legend><span class="glyphicon glyphicon-edit"></span> 选择新头像</legend>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<input type="file" id="face" name="face" />
				</div>
			</div>
			<br>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<a href="javascript:do_face_set()" type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-cloud-upload"></span> 上传头像</a>
				</div>
			</div>
		</form>
	</div>
</div>


<script src="js/ajaxfileupload.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('li#face_set').addClass('active');
});
function do_face_set()
{
	$.ajaxFileUpload({
		url:'?c=center&a=do_face_set', //你处理上传文件的服务端
			secureuri:false,
			fileElementId:'face',
			dataType: 'text',
			success: function (rs) {
				if (rs != 's0') {
					alert(rs);
					return;
				}
				alert ('成功上传了！^_^');
				get_face_set();
			}
	});

}

</script>

