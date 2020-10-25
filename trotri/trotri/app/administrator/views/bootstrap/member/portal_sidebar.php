<!-- SideBar -->
<div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar">
<?php
$config = array(
	'portal_index' => array(
		'label' => 'MOD_MEMBER_URLS_PORTAL_INDEX',
		'm' => 'member', 'c' => 'portal', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_MEMBER_URLS_PORTAL_CREATE',
			'm' => 'member', 'c' => 'portal', 'a' => 'create'
		)
	),
	'portal_trashindex' => array(
		'label' => 'MOD_MEMBER_URLS_PORTAL_TRASHINDEX',
		'm' => 'member', 'c' => 'portal', 'a' => 'trashindex'
	),
	'members' => array(
		'label' => 'MOD_MEMBER_URLS_MEMBERS_INDEX',
		'm' => 'member', 'c' => 'members', 'a' => 'index'
	),
	'social' => array(
		'label' => 'MOD_MEMBER_URLS_SOCIAL_INDEX',
		'm' => 'member', 'c' => 'social', 'a' => 'index'
	),
	'types' => array(
		'label' => 'MOD_MEMBER_URLS_TYPES_INDEX',
		'm' => 'member', 'c' => 'types', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_MEMBER_URLS_TYPES_CREATE',
			'm' => 'member', 'c' => 'types', 'a' => 'create'
		)
	),
	'ranks' => array(
		'label' => 'MOD_MEMBER_URLS_RANKS_INDEX',
		'm' => 'member', 'c' => 'ranks', 'a' => 'index',
		'icon' => array(
			'label' => 'MOD_MEMBER_URLS_RANKS_CREATE',
			'm' => 'member', 'c' => 'ranks', 'a' => 'create'
		)
	),
);

if ($this->controller === 'portal') {
	if ($this->action === 'trashindex') {
		$config['portal_trashindex']['active'] = true;
	}
	else {
		$config['portal_index']['active'] = true;
	}
}
elseif ($this->controller === 'members') {
	$config['members']['active'] = true;
}
elseif ($this->controller === 'social' || $this->controller === 'addresses') {
	$config['social']['active'] = true;
}
elseif ($this->controller === 'types') {
	$config['types']['active'] = true;
}
elseif ($this->controller === 'ranks') {
	$config['ranks']['active'] = true;
}

$this->widget('views\bootstrap\components\bar\SideBar', array('config' => $config));
?>

<?php
if ($this->controller === 'portal') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl((($this->action == 'trashindex') ? 'trashindex' : 'index'), 'portal', 'member'),
			'elements_object' => $this->elements,
			'elements' => array(
				'member_id' => array(
					'type' => 'text',
				),
				'forbidden' => array(
					'type' => 'select',
				),
				'login_type' => array(
					'type' => 'select',
				),
				'valid_mail' => array(
					'type' => 'select',
				),
				'valid_phone' => array(
					'type' => 'select',
				),
				'dt_registered_ge' => array(
					'type' => 'datetimepicker',
				),
				'dt_registered_le' => array(
					'type' => 'datetimepicker',
				),
				'dt_last_login_ge' => array(
					'type' => 'datetimepicker',
				),
				'dt_last_login_le' => array(
					'type' => 'datetimepicker',
				),
			),
			'columns' => array(
				'login_name',
				'member_id',
				'login_type',
				'member_name',
				'member_mail',
				'member_phone',
				'valid_mail',
				'valid_phone',
				'forbidden',
				'ip_registered',
				'dt_registered_ge',
				'dt_registered_le',
				'dt_last_login_ge',
				'dt_last_login_le',
				'login_count_ge',
				'login_count_le',
			)
		)
	);
}

if ($this->controller === 'members') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'members', 'member'),
			'elements_object' => $this->elements,
			'elements' => array(
				'member_id' => array(
					'type' => 'text',
				),
				'login_type' => array(
					'type' => 'select',
				),
				'type_id' => array(
					'options' => $this->elements->getTypeNames(),
				),
				'rank_id' => array(
					'options' => $this->elements->getRankNames(),
				),
			),
			'columns' => array(
				'login_name',
				'member_id',
				'login_type',
				'member_name',
				'member_mail',
				'member_phone',
				'type_id',
				'rank_id'
			)
		)
	);
}

if ($this->controller === 'social') {
	$this->widget('views\bootstrap\widgets\SearchBuilder',
		array(
			'name' => 'search',
			'action' => $this->getUrlManager()->getUrl('index', 'social', 'member'),
			'elements_object' => $this->elements,
			'elements' => array(
				'member_id' => array(
					'type' => 'text',
				),
				'login_type' => array(
					'type' => 'select',
				),
				'sex' => array(
					'type' => 'select',
				),
			),
			'columns' => array(
				'login_name',
				'member_id',
				'login_type',
				'member_name',
				'member_mail',
				'member_phone',
				'realname',
				'sex',
				'birth_md',
				'qq',
			)
		)
	);
}
?>
</div><!-- /.col-xs-6 col-sm-2 -->
<!-- /SideBar -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/member.js?v=' . $this->version); ?>
