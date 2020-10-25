<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('e0');

if (empty($_GET['blog_id'])) die;

$blog_id = intval($_GET['blog_id']);

$rs = dt_query("SELECT * FROM blog WHERE id = $blog_id");
$blog = mysql_fetch_array($rs);
if (empty($blog)) die('e0');

if ($auth['id'] != $blog['user_id']) die('e0');

?>

<?php require_once('inc/umediter_text.html'); ?> 

<div id="blog_edit">
	<div class="well">
		<div class="row clearfix">
			<div class="col-md-9 column">
				<ul class="breadcrumb" style="background-color:#fff">
					<li>
					<a href="javascript:get_blog_list()">列表</a> <span class="divider">/</span>
					</li>
					<li class="active">
					编辑	
					</li>
				</ul>
			</div>
			<div class="col-md-3 column">
				<a class="btn btn-default btn-block" href="javascript:get_blog_list()"><span class="glyphicon glyphicon-fast-backward"></span> 返回</a>
			</div>
		</div>
		<br>
		<form id="blog_edit" class="row" action="javascript:do_blog_edit(<?php echo $blog['id'] ?>)">
			<legend class="col-md-12" ><span class="glyphicon glyphicon-edit"></span> 编辑日志</legend>
			<input type="hidden" name="blog_id" value="<?php echo $blog['id'] ?>"/>
			<div class="col-md-12 form-group">
				<label>标题</label>
				<input name="title" class="form-control" required="" value="<?php echo $blog['title'] ?>"/>
			</div>
			<div class="col-md-12 form-group">
				<label>内容</label>
				<textarea id="blog_content" name="content" class="form-control" required="" style="width:104%;height:900px;"><?php echo $blog['content'] ?></textarea>
			</div>
			<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
			<div class="col-md-9">
			</div>
			<div class="col-md-3">
				<button class="btn btn-default btn-block">保存</button>
			</div>
		</form>

	</div>
</div>
<script type="text/javascript">
var blog_content = UM.getEditor("blog_content");  
</script>
