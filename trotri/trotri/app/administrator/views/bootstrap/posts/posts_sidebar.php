<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'posts_index' => array(
		'label' => 'MOD_POSTS_URLS_POSTS_INDEX',
		'm' => 'posts', 'c' => 'posts', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_POSTS_URLS_POSTS_CREATE',
			'm' => 'posts', 'c' => 'posts', 'a' => 'create'
		)
	),
	'posts_trashindex' => array(
		'label' => 'MOD_POSTS_URLS_POSTS_TRASHINDEX',
		'm' => 'posts', 'c' => 'posts', 'a' => 'trashindex'
	),
	'comments' => array(
		'label' => 'MOD_POSTS_URLS_COMMENTS_INDEX',
		'm' => 'posts', 'c' => 'comments', 'a' => 'index'
	),
	'categories' => array(
		'label' => 'MOD_POSTS_URLS_CATEGORIES_INDEX',
		'm' => 'posts', 'c' => 'categories', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_POSTS_URLS_CATEGORIES_CREATE',
			'm' => 'posts', 'c' => 'categories', 'a' => 'create'
		)
	),
	'modules' => array(
		'label' => 'MOD_POSTS_URLS_MODULES_INDEX',
		'm' => 'posts', 'c' => 'modules', 'a' => 'index',
	)
);

if ($this->controller === 'posts') {
	if ($this->action === 'trashindex') {
		$config['posts_trashindex']['active'] = true;
	}
	else {
		$config['posts_index']['active'] = true;
	}
}
elseif ($this->controller === 'comments') {
	$config['comments']['active'] = true;
}
elseif ($this->controller === 'categories') {
	$config['categories']['active'] = true;
}
elseif ($this->controller === 'modules') {
	$config['modules']['active'] = true;
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'posts') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl((($this->action == 'trashindex') ? 'trashindex' : 'index'), 'posts', 'posts'),
			'elements_object' => $this->elements,
			'elements' => array(
				'post_id' => array(
					'type' => 'text',
				),
				'category_id' => array(
					'options' => $this->elements->getCategoryNames(),
				),
				'module_id' => array(
					'options' => $this->elements->getModuleNames(),
				),
				'is_head' => array(
					'type' => 'select',
				),
				'is_recommend' => array(
					'type' => 'select',
				),
				'is_jump' => array(
					'type' => 'select',
				),
				'is_published' => array(
					'type' => 'select',
				),
				'dt_publish_up' => array(
					'type' => 'datetimepicker',
				),
				'dt_publish_down' => array(
					'type' => 'datetimepicker',
				),
				'comment_status' => array(
					'type' => 'select',
				),
				'allow_other_modify' => array(
					'type' => 'select',
				),
				'creator_id' => array(
					'type' => 'text',
				),
				'creator_name' => array(
					'type' => 'text',
				),
				'last_modifier_id' => array(
					'type' => 'text',
				),
				'last_modifier_name' => array(
					'type' => 'text',
				),
				'dt_created_ge' => array(
					'type' => 'datetimepicker',
				),
				'dt_created_le' => array(
					'type' => 'datetimepicker',
				),
				'dt_last_modified_ge' => array(
					'type' => 'datetimepicker',
				),
				'dt_last_modified_le' => array(
					'type' => 'datetimepicker',
				),
			),
			'columns' => array(
				'order',
				'title',
				'post_id',
				'alias',
				'keywords',
				'category_id',
				'module_id',
				'is_head',
				'is_recommend',
				'is_jump',
				'jump_url',
				'is_published',
				'dt_publish_up',
				'dt_publish_down',
				'comment_status',
				'allow_other_modify',
				'creator_id',
				'creator_name',
				'last_modifier_id',
				'last_modifier_name',
				'dt_created_ge',
				'dt_created_le',
				'dt_last_modified_ge',
				'dt_last_modified_le',
				'ip_created',
				'ip_last_modified',
			)
		)
	);
}

if ($this->controller === 'comments') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'comments', 'posts'),
			'elements_object' => $this->elements,
			'elements' => array(
				'post_id' => array(
					'type' => 'text',
				),
				'content' => array(
					'type' => 'text',
				),
				'author_name' => array(
					'type' => 'text',
				),
				'author_mail' => array(
					'type' => 'text',
				),
				'author_url' => array(
					'type' => 'text',
				),
				'is_published' => array(
					'type' => 'select',
				),
				'dt_created_le' => array(
					'type' => 'datetimepicker',
				),
				'dt_created_ge' => array(
					'type' => 'datetimepicker',
				),
				'ip_created' => array(
					'type' => 'text',
				),
			),
			'columns' => array(
				'post_id',
				'content',
				'author_name',
				'author_mail',
				'author_url',
				'is_published',
				'dt_created_le',
				'dt_created_ge',
				'ip_created'
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/ckeditor/ckeditor.js'); ?>
<?php echo $this->getHtml()->cssFile($this->static_url . '/plugins/jquery-upload-file/uploadpreviewimg.css'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/jquery-upload-file/jquery.uploadfile.min.js'); ?>

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/posts.js?v=' . $this->version); ?>
