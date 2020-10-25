<?php

if (empty($_GET['reply_id'])) die('e0');
$reply_id = intval($_GET['reply_id']);

?>
<div class="row clearfix topic-reply">
	<div class="col-md-12 column">
			<form class="form-horizontal" id="r_r_form">
					<input type="hidden" name="reply_id" value="<?php echo $reply_id ?>"/>
					<div class="form-group">
						<div class="col-md-12">                     
							<textarea class="form-control input-lg" id="topic_r_r_content_<?php echo $reply_id ?>" name="content" style="width:104%;height:110px"></textarea>
						</div>
					</div>
					<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
					<div class="form-group">
						<label class="col-md-10 control-label"></label>
						<div class="col-md-2">
							<a href="javascript:do_topic_r_r_add(<?php echo $reply_id ?>)" class="btn btn-default btn-block"> 发布回复 </a>
						</div>
					</div>
			</form>
	</div>
</div>
<script type="text/javascript">
topic_r_r_content[<?php echo $reply_id ?>] = UM.getEditor("topic_r_r_content_<?php echo $reply_id ?>");  
$('input[name=token]').val(<?php echo get_token() ?>);
</script>
