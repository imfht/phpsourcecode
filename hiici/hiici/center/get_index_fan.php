<?php

$user_id = intval($_GET['user_id']);

$user_info = dt_query_one("SELECT fan_c FROM user_info WHERE id = $user_id");
if (!$user_info) die('获取user_info失败！'); 

$user_infos = dt_query("SELECT id, name FROM user_info WHERE id in (SELECT user_id FROM follow WHERE to_user_id = $user_id) ORDER BY c_at DESC LIMIT 4");
if (!$user_infos) die('获取user_infos失败！'); 

?>
<div class="well index-panel">
	<a href="javascript:get_relation_index(1)" class="pull-right">全部</a>
	<h4 class="title">Ta 的粉丝 (<?php echo $user_info['fan_c'] ?>)</h4>
	<hr class="solid">
	<div class="row clearfix">
		<?php while($user_info = mysql_fetch_array($user_infos)) { ?>
		<div class="index-side-col-3">
			<a href="?c=center&user_id=<?php echo $user_info['id'] ?>">
				<img src="<?php echo FACE_URL.$user_info['id'] ?>_min.jpg" class="img-rounded user-face"/>
				<center><p><?php echo $user_info['name'] ?></p></center>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
