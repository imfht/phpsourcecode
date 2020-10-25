<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<div class="row">
			<form role="form" method="post" action="<?php echo U('Home/perform/edit'); ?>">
			<div class="col-sm-12">
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
				<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
				<button type="submit" class="btn btn-warning btn-block">
					保存
				</button>
				
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