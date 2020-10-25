<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<div class="row">
			<form role="form" method="post" action="<?php echo U('Home/perform/edit'); ?>">
			<div class="col-sm-9">
				<div class="form-group">
					<label>
						标题
					</label>
					<input name="title" type="text" class="form-control" placeholder="" value="<?php echo mc_get_page_field($_GET['id'],'title'); ?>">
				</div>
				<div class="form-group">
					<label>
						内容
					</label>
					<textarea name="content" class="form-control" rows="3"><?php echo mc_magic_out(mc_get_page_field($_GET['id'],'content')); ?></textarea>
				</div>
				<div class="form-group">
					<label>
						标签（多个标签以空格隔开）
					</label>
					<input name="tags" type="text" class="form-control" value="<?php foreach(mc_get_meta($_GET['id'],'tag',false) as $tag) : echo $tag.' '; endforeach; ?>" placeholder="">
				</div>
				<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
				<button type="submit" class="btn btn-warning btn-block">
					保存
				</button>
				
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label>
						选择分类
					</label>
					<select class="form-control" name="term">
						<?php $terms = M('page')->where('type="term_article"')->order('id desc')->select(); ?>
						<?php foreach($terms as $val) : ?>
						<option value="<?php echo $val['id']; ?>" <?php if(mc_get_meta($_GET['id'],'term')==$val['id']) echo 'selected'; ?>>
							<?php echo $val['title']; ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
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
				            $.ajax({
								type: 'POST',
								url: '<?php echo mc_site_url(); ?>/index.php?m=home&c=perform&a=publish_img',
								data:{src:this.result},
								success: function(data) {
									$('#pub-imgadd #pub-input').val(data);
								}
							});
				        } 
				} 
				</script>
			</div>
			</form>
		</div>
	</div>
	<script charset="utf-8" src="<?php echo mc_site_url(); ?>/Kindeditor/kindeditor-all-min.js"></script>
				<script>
					var editor;
					KindEditor.ready(function(K) {
						editor = K.create('textarea[name="content"]', {
							resizeType : 1,
							allowPreviewEmoticons : false,
							allowImageUpload : true,
							height : 300,
							themeType : 'simple',
							langType : 'zh-CN',
							uploadJson : '<?php echo U('Publish/index/upload'); ?>',
							items : ['source', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'clearhtml', 'quickformat', 'selectall', '|', 
					'formatblock', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
					'italic', 'underline', 'strikethrough', 'removeformat', '|', 'image', 'multiimage', 'table', 'hr', 'emoticons', 'baidumap', 'link', 'unlink'],
							afterChange : function() {
								K(this).html(this.count('text'));
							}
						});
					});
				</script>
<?php mc_template_part('footer'); ?>