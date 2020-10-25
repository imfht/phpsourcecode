<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

?>
<br>
<ul class="nav nav-tabs" id="set_nav">
	<li id="face_set">
	<a href="javascript:get_x_set('face')">头像设置</a>
	</li>
	<li id="background_set">
	<a href="javascript:get_x_set('background')">背景设置</a>
	</li>
	<li id="audio_set">
	<a href="javascript:get_x_set('audio')">空间音乐</a>
	</li>
	<li id="password_set">
	<a href="javascript:get_x_set('password')">密码设置</a>
	</li>
	<li id="email_set">
	<a href="javascript:get_x_set('email')">邮箱设置</a>
	</li>
	<li id="name_set">
	<a href="javascript:get_x_set('name')">修改名字</a>
	</li>
</ul> <br>
<div class="row clearfix" id="set_area">
</div>
<script type="text/javascript">

$(document).ready(function(){
	$('li#set').addClass('active');
	get_x_set('face');
});
function get_x_set(x) {
	$.get('?c=center&a=get_'+x+'_set', function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('ul#set_nav').find('li').removeClass('active');
		$('div#set_area').html(rs);
	});
}
</script>

