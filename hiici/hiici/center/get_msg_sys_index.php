<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET)) die;

$page = @intval($_GET['page']);

$cond = "WHERE to_user_id = ".$auth['id'];
if (empty($page)) { $page = 1; }
$limit = 20;

$msg_syss = dt_query("SELECT content, c_at FROM msg_sys $cond ORDER BY c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!$msg_syss) die('获取数据失败！');

$rs = dt_query("UPDATE user_info SET msg_sys_c = 0 WHERE id = ".$auth['id']);
if (!$rs) return false;

?>

<div class="well center-well">
	<div class="row clearfix">
		<div class="col-lg-9 col-sm-8 column">
		</div>
		<div class="col-lg-3 col-sm-4 column">
			<h3><span class="glyphicon glyphicon-envelope"></span> 系统信息</h3>
		</div>
	</div>
	<hr>
	<table class="table table-hover">
		<thead>
			<tr>
				<th><span class="glyphicon glyphicon-list"></span> 内容</th>
				<th>接收时间</th>
			</tr>
		</thead>
		<tbody>
		<?php while($msg_sys = mysql_fetch_array($msg_syss)) { ?>
		<tr>
			<td><?php echo $msg_sys['content'] ?></td>
			<td><?php echo fmt_date($msg_sys['c_at']) ?></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<?php pagination('msg_sys', $cond, $page, $limit, 'javascript:get_msg_sys_index(', ')') ?>
<script type="text/javascript">
$(document).ready(function(){
	$('li#msg_sys').addClass('active');
	$('span#msg_sys_c').remove();
});
</script>
