<?php 

if (empty($_GET['shuo_id'])) die('e0');

$shuo_id = intval($_GET['shuo_id']);
$s_r_c_at = strtotime($_GET['s_r_c_at']);

$limit = 5;

$s_r_c_at = (!empty($s_r_c_at)) ? $s_r_c_at : '1000000000' ;
$shuo_replys = dt_query("SELECT * FROM shuo_reply WHERE shuo_id = $shuo_id AND c_at > $s_r_c_at ORDER BY c_at LIMIT $limit");
if (!$shuo_replys) die('e0');


?>
<?php while($shuo_reply = mysql_fetch_array($shuo_replys)) { ?>
<div class="row clearfix reply" id="shuo_reply_<?php echo $shuo_reply['id'] ?>">
	<div class="shuo-content">
		<div class="text-content"><b><?php echo $shuo_reply['user_name'] ?> </b><?php echo $shuo_reply['content'] ?></div>
		<h5>
			<span class="glyphicon glyphicon-time"></span><span id="shuo_reply_c_at"><?php echo fmt_date($shuo_reply['c_at']) ?></span>
			<a class="pull-right" href="javascript:shuo_r_r(<?php echo $shuo_id ?>, <?php echo $shuo_reply['user_id'] ?>, '<?php echo $shuo_reply['user_name'] ?>')">回复</a>
			<a class="pull-right" href="javascript:do_shuo_reply_up(<?php echo $shuo_reply['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span>赞(<span id="shuo_reply_up_c"><?php echo $shuo_reply['up_c'] ?></span>)</a>
		</h5>
	</div>
	<div class="userface" >
		<a href="?c=center&user_id=<?php echo $shuo_reply['user_id'] ?>"><img src="<?php echo FACE_URL.$shuo_reply['user_id'] ?>_min.jpg" class="img-circle" /></a>
	</div>
</div>	
<?php } ?>
