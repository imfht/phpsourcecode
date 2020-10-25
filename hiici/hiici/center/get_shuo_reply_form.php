<?php

if (empty($_GET['shuo_id'])) die('e0');
$shuo_id = intval($_GET['shuo_id']);

 ?>
<div class="row clearfix" id="shuo_reply_form"> 
	<div class="col-md-12 column"> 
		<div class="well replyform"> 
			<form class="form-horizontal"> 
				<fieldset> 
					<input type="hidden" name="shuo_id" value="<?php echo $shuo_id ?>"/>
					<input type="hidden" name="to_user_id" value/>
					<div class="form-group"> 
						<div class="col-md-12">                      
							<textarea class="form-control input-lg" id="shuo_reply_content_<?php echo $shuo_id ?>" name="content" style="width:105%;height:80px;"></textarea> 
						</div> 
					</div> 
					<!-- token -->
					<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
					<div class="form-group"> 
						<label class="col-md-9 control-label"></label> 
						<div class="col-md-3"> 
							<a href="javascript:do_shuo_reply(<?php echo $shuo_id ?>)" class="btn btn-default btn-block"> 发布回复 </a> 
						</div> 
					</div> 
				</fieldset> 
			</form> 
		</div> 
	</div> 
</div> 
<script type="text/javascript">
shuo_reply_content[<?php echo $shuo_id ?>] = UM.getEditor("shuo_reply_content_<?php echo $shuo_id ?>");  
</script>
