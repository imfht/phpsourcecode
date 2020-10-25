<?php 
$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['user_id'])) die;

$user_id = intval($_GET['user_id']);
$msg_c_at = strtotime($_GET['msg_c_at']);
$is_new = @intval($_GET['is_new']);

$msg_c_at = (!empty($msg_c_at)) ? $msg_c_at : '2140000000';
$direct = (1 == $is_new) ? '>' : '<';

$msgs = dt_query("SELECT * FROM msg WHERE ((user_id = $user_id AND to_user_id = ".$auth['id'].") OR (user_id = ".$auth['id']." AND to_user_id = $user_id)) AND c_at $direct $msg_c_at ORDER BY c_at DESC LIMIT 10");
if (!$msgs) die('获取数据失败！');

?>
<?php while($msg = mysql_fetch_array($msgs)) { ?>
<div class="msg-item" id="msg_<?php echo $msg['id'] ?>">
<?php if ($auth['id'] != $msg['user_id']) { ?>
	<div class="panel other">
		<div class="userface">
			<a href="?c=center&user_id=<?php echo $msg['user_id'] ?>"><img src="<?php echo FACE_URL.$msg['user_id'] ?>_min.jpg" class="img-circle" /></a>
		</div>
		<div class="content">
			<h4 class="pull-right"><strong><?php echo $msg['user_name'] ?></strong></h4>
			<h4 class="time"> <span class="glyphicon glyphicon-time"></span><span id="msg_c_at"><?php echo fmt_date($msg['c_at']) ?></span></h4>
			<div class="text-content pull-right" style="margin-top:0px"><?php echo $msg['content'] ?></div>
		</div>
	</div>
<?php } else { ?>
	<div class="panel self">
		<a href="javascript:do_msg_del(<?php echo $msg['id'] ?>)" class="close">&times;</a>
		<div class="content">
			<h4 class="pull-right time"> <span class="glyphicon glyphicon-time"></span><span id="msg_c_at"><?php echo fmt_date($msg['c_at']) ?></span></h4>
			<h4><strong><?php echo $msg['user_name'] ?></strong></h4>
			<div class="text-content"><?php echo $msg['content'] ?></div>
		</div>
		<div class="userface">
			<img src="<?php echo FACE_URL.$msg['user_id'] ?>_min.jpg" class="img-circle"/>
		</div>
	</div>
<?php } ?>
</div>
<?php } ?>
