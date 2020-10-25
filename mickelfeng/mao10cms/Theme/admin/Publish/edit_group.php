<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<div class="row">
			<form role="form" method="post" action="<?php echo U('Home/perform/edit'); ?>">
			<div class="col-sm-9">
				<div class="form-group">
					<label>
						群组名称
					</label>
					<input name="title" type="text" class="form-control" placeholder="" value="<?php echo mc_get_page_field($_GET['id'],'title'); ?>">
				</div>
				<div class="form-group">
					<label>
						群组描述
					</label>
					<textarea name="content" class="form-control" rows="7"><?php echo mc_magic_out(mc_get_page_field($_GET['id'],'content')); ?></textarea>
				</div>
				<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
				<button type="submit" class="btn btn-warning btn-block">
					保存
				</button>
				
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>
							封面图片
					</label>
					<div id="pub-imgadd">
						<img class="default-img" id="default-img" src="<?php if(mc_fmimg($_GET['id'])) echo mc_fmimg($_GET['id']); else echo mc_theme_url().'/img/upload.jpg'; ?>">
						<input type="hidden" name="fmimg" id="pub-input" value="<?php if(mc_fmimg($_GET['id'])) echo mc_fmimg($_GET['id']); else echo mc_theme_url().'/img/upload.jpg'; ?>">
						<input type="file" id="picfile" onchange="readFile(this,1)" />
					</div>
				</div>
				<script>
					function readFile(obj,id){ 
				        var file = obj.files[0]; 	
				        //判断类型是不是图片
				        if(!/image\/\w+/.test(file.type)){   
				                alert("请确保文件为图像类型"); 
				                return false; 
				        } 
				        var reader = new FileReader(); 
				        reader.readAsDataURL(file); 
				        reader.onload = function(e){ 
				        	$('#pub-imgadd img').attr('src',this.result);
				        	$('#pub-imgadd #pub-input').val(this.result);
				            //alert(this.result);
				        } 
				} 
				</script>
			</div>
			</form>
		</div>
	</div>
<?php mc_template_part('footer'); ?>