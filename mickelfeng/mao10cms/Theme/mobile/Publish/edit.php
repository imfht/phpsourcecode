<?php mc_template_part('header'); ?>
	<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/editor/summernote.css">
	<script src="<?php echo mc_site_url(); ?>/editor/summernote.min.js"></script>
	<script src="<?php echo mc_site_url(); ?>/editor/summernote-zh-CN.js"></script>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<ol class="breadcrumb mb-20 mt-40">
					<li>
						<a href="<?php echo U('home/index/index'); ?>">
							首页
						</a>
					</li>
					<li>
						<a href="<?php echo U('post/group/index'); ?>">
							社区
						</a>
					</li>
					<li>
						<a href="<?php echo U('post/group/single?id='.mc_get_meta($_GET['id'],'group')); ?>">
							<?php echo mc_get_page_field(mc_get_meta($_GET['id'],'group'),'title'); ?>
						</a>
					</li>
					<li class="active">
						编辑主题
					</li>
				</ol>
				<form role="form" method="post" action="<?php echo U('Home/perform/edit'); ?>" onsubmit="return postForm()">
					<div class="row">
						<div class="col-sm-4 col-lg-3">
							<div class="form-group">
								<label>
									版块
								</label>
								<select class="form-control" name="group">
								<?php $group = M('page')->where('type="group"')->order('date desc')->select(); if($group) : foreach($group as $val) : ?>
									<option value="<?php echo $val['id']; ?>" <?php if($_GET['group']==$val['id']) echo 'selected'; ?>><?php echo $val['title']; ?></option>
								<?php endforeach; endif; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-8 col-lg-9">
							<div class="form-group">
								<label>
									标题
								</label>
								<input name="title" type="text" class="form-control" placeholder="" value="<?php echo mc_get_page_field($_GET['id'],'title'); ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>
							主题内容
						</label>
						<textarea name="content" class="form-control" rows="3" id="summernote"><?php echo mc_magic_out(mc_get_page_field($_GET['id'],'content')); ?></textarea>
					</div>
					<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
					<button type="submit" class="btn btn-warning btn-block">
						保存
					</button>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
			$(document).ready(function() {
					$('#summernote').summernote({
						height: "500px",
						lang:"zh-CN",
						toolbar: [
						    ['style', ['style']],
						    ['color', ['color']],
						    ['font', ['bold', 'underline', 'clear']],
						    ['para', ['ul', 'paragraph']],
						    ['table', ['table']],
						    ['insert', ['link', 'picture']],
						    ['misc', ['codeview', 'fullscreen']]
						],
						onImageUpload: function(files) {
						    var file = files[0]; 	
					        //判断类型是不是图片
					        if(!/image\/\w+/.test(file.type)){   
					                alert("请确保文件为图像类型"); 
					                return false; 
					        }
					        var reader = new FileReader(); 
					        reader.readAsDataURL(file); 
					        reader.onload = function(e){ 
					        	//alert(this.result);
					        	$.ajax({
									type: 'POST',
									url: '<?php echo mc_site_url(); ?>/index.php?m=home&c=perform&a=publish_img',
									data:{src:this.result},
									success: function(data) {
										$("#summernote").summernote("insertImage", data, file.name);
									}
								});
					        } 
						}
					});
				});
				var postForm = function() {
					var content = $('textarea[name="content"]').html($('#summernote').code());
				};
				</script>
<?php mc_template_part('footer'); ?>