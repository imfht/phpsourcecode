<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<ol class="breadcrumb mb-20 mt-20" id="baobei-term-breadcrumb">
			<li>
				<a href="<?php echo U('home/index/index'); ?>">
					首页
				</a>
			</li>
			<li>
				<a href="<?php echo U('post/group/index'); ?>">
					群组
				</a>
			</li>
			<li class="active">
				新建群组
			</li>
			<div class="pull-right">
				<a href="<?php echo U('publish/index/add_group'); ?>" class="active">新建群组</a>
			</div>
		</ol>
		<div class="row">
			<form role="form" method="post" action="<?php echo U('home/perform/publish_group'); ?>">
			<div class="col-sm-9">
				<div class="form-group">
					<label>
						群组名称
					</label>
					<input name="title" type="text" class="form-control" placeholder="">
				</div>
				<div class="form-group">
					<label>
						群组描述
					</label>
					<textarea name="content" class="form-control" rows="7"></textarea>
				</div>
				<button type="submit" class="btn btn-warning btn-block">
					<i class="glyphicon glyphicon-ok"></i> 提交
				</button>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>
							封面图片
					</label>
					<div id="pub-imgadd">
						<img class="default-img" id="default-img" src="<?php echo mc_theme_url(); ?>/img/upload.jpg">
						<input type="hidden" name="fmimg" id="pub-input" value="">
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