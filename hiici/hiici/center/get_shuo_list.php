<?php 

if (empty($_GET)) die('e0');

$user_id = intval($_GET['user_id']);
$shuo_c_at = strtotime($_GET['shuo_c_at']);
$my_shuo = (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) ? @intval($_GET['my_shuo']) : 1;

$shuo_c_at = (!empty($shuo_c_at)) ? $shuo_c_at : '2140000000' ;
$cond_f = (1 != $my_shuo) ? "user_id in (SELECT to_user_id FROM follow WHERE user_id = $user_id) OR" : '';
$limit = 10;

$shuos = dt_query("SELECT * FROM shuo WHERE ($cond_f user_id = $user_id) AND c_at < $shuo_c_at ORDER BY c_at DESC LIMIT $limit");
if (!$shuos) die('e0');

?>
<?php while($shuo = mysql_fetch_array($shuos)) { ?>
<div class="well comment" id="shuo_<?php echo $shuo['id'] ?>">
	<div class="row clearfix">
		<div class="shuo-content">
			<div class="text-content"><b><?php echo $shuo['user_name'] ?> </b><?php echo $shuo['content'] ?></div>
			<h5>
				<span class="glyphicon glyphicon-time"></span><span id="shuo_c_at"> <?php echo fmt_date($shuo['c_at']) ?></span>
				<a class="pull-right" href="javascript:get_shuo_reply_form(<?php echo $shuo['id'] ?>)">回复(<span id="shuo_reply_c"><?php echo $shuo['reply_c'] ?></span>)</a>
				<a class="pull-right hidden-xs" href="javascript:do_shuo_up(<?php echo $shuo['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span>赞(<span id="shuo_up_c"><?php echo $shuo['up_c'] ?></span>)</a>
				<?php if (!empty($_SESSION['auth']) && $_SESSION['auth']['id'] == $shuo['user_id']) { ?>
				<a class="pull-right hidden-xs del" href="javascript:do_shuo_del(<?php echo $shuo['id'] ?>)"><span class="glyphicon glyphicon-remove-circle"></span>删除</a>
				<?php } ?>
			</h5>
		</div>
		<div class="userface">
			<a href="?c=center&user_id=<?php echo $shuo['user_id'] ?>"><img src="<?php echo FACE_URL.$shuo['user_id'] ?>_min.jpg" class="img-circle" /></a>
		</div>
	</div>
</div>
<?php } ?>
