<?php
$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');
?>

<?php require_once('inc/umediter_text.html'); ?> 

<div id="blog_add">
	<div class="well">
		<div class="row clearfix">
			<div class="col-md-9 column">
				<ul class="breadcrumb" style="background-color:#fff">
					<li>
					<a href="javascript:get_blog_list()">列表</a> <span class="divider">/</span>
					</li>
					<li class="active">
					新日志	
					</li>
				</ul>
			</div>
			<div class="col-md-3 column">
				<a class="btn btn-default btn-block" href="javascript:get_blog_list()"><span class="glyphicon glyphicon-fast-backward"></span> 返回</a>
			</div>
		</div>
		<br>
		<form id="blog_add" class="row" action="javascript:do_blog_add()">
			<legend class="col-md-12" ><span class="glyphicon glyphicon-edit"></span> 新日志</legend>
			<input type="hidden" name="id" value/>
			<div class="col-md-12 form-group">
				<label>标题</label>
				<input name="title" class="form-control"  required=""/>
			</div>
			<div class="col-md-12 form-group">
				<label>内容</label>
				<textarea id="blog_content" name="content" class="form-control" required="" style="width:104%;height:900px;"></textarea>
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
