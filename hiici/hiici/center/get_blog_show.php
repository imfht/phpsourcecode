<?php 

if (empty($_GET['blog_id'])) die;

$blog_id = intval($_GET['blog_id']);
$offset = @intval($_GET['offset']);

$cond = "WHERE id = $blog_id";

if (!empty($offset)) {
	$rs = dt_query("SELECT user_id, c_at FROM blog $cond");
	$blog = mysql_fetch_array($rs);
	if (empty($blog)) die('e0');

	switch ($offset) {
	case -1:
		$cond = "WHERE c_at < '".$blog['c_at']."' AND user_id = ".$blog['user_id']." ORDER BY c_at DESC LIMIT 1";
		break;
	case 1:
		$cond = "WHERE c_at > '".$blog['c_at']."' AND user_id = ".$blog['user_id']." ORDER BY c_at LIMIT 1";
		break;
	}
}

$rs = dt_query("UPDATE blog SET view_c = view_c + 1 WHERE id = $blog_id");
if (!$rs) die('统计访问数失败！');

$blog = dt_query_one("SELECT * FROM blog $cond");
if (!$blog) die('e0');

?>

<?php require_once('inc/umediter_varchar.html'); ?> 

<div id="blog_show">
	<div class="well center-well">
		<div class="row clearfix">
			<div class="col-md-9 column">
				<ul class="breadcrumb">
					<li>
					<a href="javascript:get_blog_list()">列表</a> <span class="divider">/</span>
					</li>
					<li class="active">
					正文	
					</li>
				</ul>
			</div>
			<div class="col-md-3 column">
				<a class="btn btn-default btn-block" href="javascript:get_blog_list()"><span class="glyphicon glyphicon-fast-backward"></span> 返回</a>
			</div>
		</div>
		<div id="blog_<?php echo $blog['id'] ?>">
			<div class="row">
				<div class="col-md-8 column">
					<a class="btn btn-default" href="javascript:do_blog_up(<?php echo $blog['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span> 赞(<span id="blog_up_c"><?php echo $blog['up_c'] ?></span>)</a>
				</div>
				<div class="col-md-4 column">
					<a class="btn btn-default pull-right" style="margin-left:6px" href="javascript:get_blog_show(<?php echo $blog['id'] ?>, 1)"><span class="glyphicon glyphicon-chevron-right"></span> 下一篇</a>
					<a class="btn btn-default pull-right" href="javascript:get_blog_show(<?php echo $blog['id'] ?>, -1)"><span class="glyphicon glyphicon-chevron-left"></span> 上一篇</a>
				</div>
			</div>
			<hr>

			<div class="well">
				<center><h3><?php echo $blog['title'] ?></h3></center>
				<center><h4><small>发布于：<?php echo fmt_date($blog['c_at']) ?> | 阅读：<?php echo $blog['view_c'] ?> 次</small></h4></center>
				<br>
				<div class="text-content"><?php echo $blog['content'] ?></div>
			</div>

			<hr>
			<div class="row">
				<div class="col-md-8 column">
					<?php if (!empty($_SESSION['auth']) && $blog['user_id'] == $_SESSION['auth']['id']) { ?>
					<a class="btn btn-default" href="javascript:get_blog_edit(<?php echo $blog['id'] ?>)"><span class="glyphicon glyphicon-edit"></span> 编辑</a>
					<a class="btn btn-default" href="javascript:do_blog_del(<?php echo $blog['id'] ?>)"><span class="glyphicon glyphicon-remove"></span> 删除</a>
					<?php } ?>
				</div>
				<div class="col-md-4 column">
					<a class="btn btn-default pull-right" href="javascript:do_blog_up(<?php echo $blog['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span> 赞(<span id="blog_up_c"><?php echo $blog['up_c'] ?></span>)</a>
				</div>
			</div>
		</div>
	</div>
	<div class="well center-well">
		<?php if (!empty($_SESSION['auth'])) { ?>
		<div class="row clearfix">
			<div class="col-md-12 column">
				<div style="padding-bottom:3px;" id="blog_reply_form">
					<form class="form-horizontal" id="blog_reply">
						<fieldset>
							<input type="hidden" name="blog_id" value="<?php echo $blog_id ?>"/>
							<input type="hidden" name="to_user_id" value/>
							<div class="form-group">
								<div class="col-md-12">                     
									<textarea class="form-control input-lg" id="blog_reply_content" name="content" style="width:105%;height:90px"></textarea>
								</div>
							</div>
							<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
							<div class="form-group">
								<label class="col-md-9 control-label" for="singlebutton"></label>
								<div class="col-md-3">
									<a href="javascript:do_blog_reply()" class="btn btn-default btn-block"> 发布评论 </a>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<?php } ?>
		<div id="blog_reply_list_area">

			<!-- 评论加载区域 -->

		</div>
	</div>
</div>
<script type="text/javascript">

<?php if (!empty($_SESSION['auth'])) { ?>
var blog_reply_content = UM.getEditor("blog_reply_content");  
<?php } ?>

$(document).ready(function(){
	get_blog_reply_list();
});

function get_blog_reply_list(page) {
	$.get('?c=center&a=get_blog_reply_list&blog_id=<?php echo $blog_id ?>&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#blog_reply_list_area').html(rs);
	});
}

</script>
