<?php mc_template_part('header'); ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2" id="group">
				<ul class="nav nav-tabs mb-10 post-nav">
					<li role="presentation">
						<a href="<?php echo U('post/group/index'); ?>">
							社区首页
						</a>
					</li>
					<?php $groups = M('page')->where('type="group"')->order('date desc')->select(); if($groups) : foreach($groups as $val) : ?>
					<li role="presentation" class="<?php if($_GET['id']==$val['id']) echo 'active'; ?>">
						<a href="<?php echo U('post/group/single?id='.$val['id']); ?>">
							<?php echo $val['title']; ?>
						</a>
					</li>
					<?php endforeach; endif; ?>
					<li class="pull-right">
						<a href="javascript:;">共有<?php echo $count; ?>个主题</a>
					</li>
				</ul>
				<div class="panel panel-default mb-10">
					<div class="panel-body">
						<?php echo mc_magic_out(mc_get_page_field($_GET['id'],'content')); ?>
					</div>
				</div>
				<?php if($page) : ?>
				<div id="post-list-default">
					<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
						<div class="row">
							<div class="col-sm-6 col-md-7 col-lg-8">
								<div class="media">
									<?php $author = mc_get_meta($val['id'],'author',true); ?>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><span class="wto"><?php echo $val['title']; ?></span><?php if(mc_get_page_field($val['id'],'date')>strtotime("now")) : ?><span class="label label-danger">置顶</span><?php endif; ?></a>
										</h4>
										<p class="post-info wto">
											<i class="glyphicon glyphicon-user"></i><a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
											<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',mc_get_meta($val['id'],'time')); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
					</ul>
					<?php echo mc_pagenavi($count,$page_now); ?>
				</div>
				<?php else : ?>
				<div id="post-list-default">
					<ul class="list-group">
						<li class="list-group-item text-center" style="padding:120px 0;">
							暂无任何话题！
						</li>
					</ul>
				</div>
				<?php endif; ?>
				<link rel="stylesheet" href="<?php echo mc_site_url(); ?>/editor/summernote.css">
				<script src="<?php echo mc_site_url(); ?>/editor/summernote.min.js"></script>
				<script src="<?php echo mc_site_url(); ?>/editor/summernote-zh-CN.js"></script>
				<form role="form" method="post" action="<?php echo U('home/perform/publish'); ?>" onsubmit="return postForm()">
					<div class="form-group">
						<label>
							标题
						</label>
						<input name="title" type="text" class="form-control" placeholder="">
					</div>
					<div class="form-group">
						<label>
							主题内容
						</label>
						<textarea name="content" class="form-control" id="summernote" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok"></i> 提交
					</button>
					<input type="hidden" name="group" value="<?php echo $id; ?>">
				</form>
				<script type="text/javascript">
			$(document).ready(function() {
					$('#summernote').summernote({
						height: "300px",
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
			</div>
		</div>
	</div>
	<?php if(mc_is_admin()) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title" id="myModalLabel">
						
					</h4>
				</div>
				<div class="modal-body text-center">
					<p>确认要删除此版块吗？</p>
					注意：当前版块下的所有话题都会被删除！
				</div>
				<div class="modal-footer" style="text-align:center;">
					<form method="post" action="<?php echo U('home/perform/delete'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<?php endif; ?>
<?php mc_template_part('footer'); ?>