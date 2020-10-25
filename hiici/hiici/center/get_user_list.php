<?php 

if (empty($_GET)) die;

$search = @filter_var($_GET['search'], FILTER_SANITIZE_STRING);
$page = @intval($_GET['page']);

if (empty($page)) { $page = 1; }
$limit = 20;

$cond = "WHERE name LIKE '%$search%'";

$user_infos = dt_query("SELECT id, name, intro FROM user_info $cond ORDER BY c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!$user_infos) die('获取数据失败！');

?>
<div class="container find-user">
	<div class="row clearfix">
		<div class="col-md-2 col-xs-2 column">
		</div>
		<div class="col-md-4 col-xs-8 column">
			<form id="search" action="javascript:get_user_list()">	
				<div class="input-group">
					<input id="find_user_search" class="form-control" type="text" placeholder="搜索" value="<?php echo $search ?>">
					<a href="javascript:onclick=$('form#search').submit()" class="input-group-addon btn"><span class="glyphicon glyphicon-search"></span></a>
				</div>
			</form>	
		</div>
	</div>
</div>
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
	<?php pagination('user_info', $cond, $page, $limit, 'javascript:get_user_list(', ')') ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#find_user').addClass('active');
});
</script>
