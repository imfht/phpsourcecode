<?php

$user_id = intval($_GET['user_id']);

$cond = "WHERE to_user_id = $user_id AND m_at > ".(time()-31*24*3600);
$visitor_c = dt_count('visitor', $cond);

$visitors = dt_query("SELECT user_id, user_name, m_at FROM visitor $cond ORDER BY m_at DESC LIMIT 4");
if (!$visitors) die('获取visitors失败！'); 

?>
<div class="well index-panel">
	<h4 class="title">最近来访 (<?php echo $visitor_c ?>)</h4>
	<hr class="solid">
	<div class="row clearfix">
		<?php while($visitor = mysql_fetch_array($visitors)) { ?>
		<div class="index-side-col-3">
			<a href="?c=center&user_id=<?php echo $visitor['user_id'] ?>">
				<img src="<?php echo FACE_URL.$visitor['user_id'] ?>_min.jpg" class="img-rounded user-face"/>
				<center><p><?php echo $visitor['user_name'] ?></p><p><?php echo date("m-d",$visitor['m_at']) ?></p></center>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
