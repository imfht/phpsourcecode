<?php 

if (empty($_GET)) die;

$user_id = intval($_GET['user_id']);
$page = @intval($_GET['page']);

$cond = "WHERE user_id = ".$user_id;
if (empty($page)) { $page = 1; }
$limit = 20;

$blogs = dt_query("SELECT id, title, up_c, view_c, c_at FROM blog $cond ORDER BY c_at DESC LIMIT ".$limit * ($page - 1).",$limit");
if (!$blogs) die('获取数据失败！');

?>

<div id="blog_list">
	<div class="well center-well" style="margin-bottom:0px;">
		<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
		<div class="row clearfix">
			<div class="col-md-9 column">
			</div>
			<div class="col-md-3 column">
				<a class="btn btn-default btn-lg btn-block" href="javascript:get_blog_add()"><span class="glyphicon glyphicon-edit"></span> 写日志</a>
			</div>
		</div>
		<hr>
		<?php } ?>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>标题</th>
					<th>阅读</th>
					<th><span class="glyphicon glyphicon-thumbs-up"></span>赞</th>
					<th>发布于</th>
				</tr>
			</thead>
			<tbody>
			<?php while($blog = mysql_fetch_array($blogs)) { ?>
			<tr>
				<td><a class="blog-item" href="javascript:get_blog_show(<?php echo $blog['id'] ?>)"><?php echo $blog['title'] ?></a></td>
				<td><?php echo $blog['view_c'].'次' ?></td>
				<td><span class="label label-default"><?php echo $blog['up_c'] ?></span></td>
				<td><?php echo fmt_date($blog['c_at']) ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php pagination('blog', $cond, $page, $limit, 'javascript:get_blog_list(', ')') ?>
</div>

