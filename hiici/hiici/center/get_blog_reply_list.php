<?php 

if (empty($_GET)) die;

$blog_id = intval($_GET['blog_id']);
$page = @intval($_GET['page']);

$cond = "WHERE blog_id = $blog_id";
if (empty($page)) { $page = 1; }
$limit = 10;

$blog_replys = dt_query("SELECT * FROM blog_reply $cond ORDER BY c_at LIMIT ".$limit * ($page - 1).",$limit");
if (!$blog_replys) die('获取数据失败！');

//分页
$pageC = ceil(dt_count('blog_reply', $cond) / $limit);
$pages = array();
for ($i = $page - 2; $i <= $page + 2; $i++) { if ($i >= 1 && $i <= $pageC) { $pages[$i] = $i; } }

?>
<legend><span class="glyphicon glyphicon-share"></span> 评论</legend>
<?php while($blog_reply = mysql_fetch_array($blog_replys)) { ?>
<div class="row clearfix" id="blog_reply_<?php echo $blog_reply['id'] ?>">
	<div class="shuo-content">
		<div class="text-content"><b><?php echo $blog_reply['user_name'] ?> </b><?php echo $blog_reply['content'] ?></div>
		<h5>
			<span class="glyphicon glyphicon-time"></span><?php echo fmt_date($blog_reply['c_at']) ?>
			<a class="pull-right" href="javascript:blog_r_r(<?php echo $blog_reply['user_id'] ?>, '<?php echo $blog_reply['user_name'] ?>')">回复</a>
			<span class="pull-right" >&nbsp&nbsp&nbsp</span>
			<a class="pull-right" href="javascript:do_blog_reply_up(<?php echo $blog_reply['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span>赞(<span id="blog_reply_up_c"><?php echo $blog_reply['up_c'] ?></span>)</a>
		</h5>
	</div>
	<div class="userface" >
		<a href="?c=center&user_id=<?php echo $blog_reply['user_id'] ?>"><img src="<?php echo FACE_URL.$blog_reply['user_id'] ?>_min.jpg" class="img-circle" width="70px"/></a>
	</div>
</div>
<hr style="margin:5px">
<?php } ?>
<ul class="pagination">
	<li> <a href="javascript:get_blog_reply_list(<?php echo $page - 1; ?>)">上一页</a> </li>
	<?php foreach ($pages as $p) { ?>
	<li <?php if ($p == $page) { ?> class="active" <?php } ?> > <a href="javascript:get_blog_reply_list(<?php echo $p; ?>)"><?php echo $p; ?></a> </li>
	<?php } ?>
	<li> <a href="javascript:get_blog_reply_list(<?php if ($page + 1 > $pageC) { ?><?php echo $pageC; ?><?php } else { ?><?php echo $page + 1; ?><?php } ?>)">下一页</a> </li>
</ul>
