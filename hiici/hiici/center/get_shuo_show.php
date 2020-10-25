<?php 

if (empty($_GET)) die('e0');

$shuo_id = intval($_GET['shuo_id']);

$shuo = dt_query_one("SELECT * FROM shuo WHERE id = $shuo_id");
if (!$shuo) die('e0');

?>
<br>
<div class="row clearfix">
	<div class="col-md-9 column">
		<ul class="breadcrumb">
			<li>
			<a href="javascript:get_shuo_index()">说说</a> <span class="divider">/</span>
			</li>
			<li class="active">
			详细	
			</li>
		</ul>
	</div>
	<div class="col-md-3 column">
		<a class="btn btn-default btn-block" href="javascript:get_shuo_index()"><span class="glyphicon glyphicon-fast-backward"></span> 返回</a>
	</div>
</div>
<div class="well comment" id="shuo_<?php echo $shuo['id'] ?>">
	<div class="row clearfix">
		<div class="shuo-content">
			<h4 class="text-content"><b><?php echo $shuo['user_name'] ?> </b><?php echo $shuo['content'] ?></h4>
			<h5>
				<span class="glyphicon glyphicon-time"></span><span id="shuo_c_at"><?php echo fmt_date($shuo['c_at']) ?></span>
				<a class="pull-right" href="javascript:get_shuo_reply_form(<?php echo $shuo['id'] ?>)">回复(<span id="shuo_reply_c"><?php echo $shuo['reply_c'] ?></span>)</a>
				<span class="pull-right" >&nbsp&nbsp&nbsp</span>
				<a class="pull-right" href="javascript:do_shuo_up(<?php echo $shuo['id'] ?>)"><span class="glyphicon glyphicon-thumbs-up"></span>赞(<span id="shuo_up_c"><?php echo $shuo['up_c'] ?></span>)</a>
				<span class="pull-right" >&nbsp&nbsp&nbsp</span>
				<?php if (!empty($_SESSION['auth']) && $_SESSION['auth']['id'] == $shuo['user_id']) { ?>
				<a class="pull-right" href="javascript:do_shuo_del(<?php echo $shuo['id'] ?>)"><span class="glyphicon glyphicon-remove-circle"></span>删除</a>
				<?php } ?>
			</h5>
		</div>
		<div class="userface">
			<a href="?c=center&user_id=<?php echo $shuo['user_id'] ?>"><img src="img/center/user_face/<?php echo $shuo['user_id'] ?>_min.jpg" class="img-circle" /></a>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	get_shuo_reply_form(<?php echo $shuo['id'] ?>);
});
</script>
