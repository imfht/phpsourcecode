<?php 

if (empty($_GET)) die;

$user_id = intval($_GET['user_id']);
$page = @intval($_GET['page']);

if (empty($page)) { $page = 1; }
$limit = 20;

$user_infos = dt_query("SELECT id, name, intro FROM user_info WHERE id in (SELECT to_user_id FROM follow WHERE user_id = $user_id) ORDER BY c_at LIMIT ".$limit * ($page - 1).",$limit");
if (!$user_infos) die('获取数据失败！');

?>
<div class="follow clearfix">
	<?php while($user_info = mysql_fetch_array($user_infos)) { ?>
	<div class="userR">
		<a target="_blank" href="?c=center&user_id=<?php echo $user_info['id'] ?>" class="btn btn-default btn-block">
			<img class="img-circle" src="<?php echo FACE_URL.$user_info['id'] ?>.jpg"/>
			<h4 class="fans-intro"><?php echo $user_info['name'] ?></h4>
			<h4 class="fans-intro"><?php echo $user_info['intro'] ?></h4>
		</a>
	</div>
	<?php } ?>
</div>
<div class="col-md-12 column">
	<?php pagination('follow', "WHERE user_id = $user_id", $page, $limit, 'javascript:get_follow_list(', ')') ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#my_follow').addClass('active');
});
</script>
