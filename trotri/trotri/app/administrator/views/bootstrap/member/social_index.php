<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getLoginNameLink($data)
	{
		return $this->elements_object->getLoginNameLink($data);
	}

	public function getHeadPortraitPreview($data)
	{
		$imgHtml = $this->html->img($data['head_portrait'], '', array('width' => 100, 'height' => 100));
		return $this->html->a($imgHtml, $data['head_portrait'], array('target' => '_blank'));
	}

	public function getSexLang($data)
	{
		return $this->elements_object->getSexLangBySex($data['sex']);
	}

	public function getAddresses($data)
	{
		$params = array('member_id' => $data['member_id']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'addresses', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_MEMBER_URLS_ADDRESSES_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'addresses', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_MEMBER_URLS_ADDRESSES_CREATE,
		));

		$output = $indexIcon . $createIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['member_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);

		$output = $modifyIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/social_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'login_name' => array(
				'callback' => 'getLoginNameLink'
			),
			'head_portrait' => array(
				'callback' => 'getHeadPortraitPreview'
			),
			'sex' => array(
				'callback' => 'getSexLang'
			),
			'addresses' => array(
				'callback' => 'getAddresses'
			),
		),
		'columns' => array(
			'login_name',
			'realname',
			'head_portrait',
			'sex',
			'birth_md',
			'telephone',
			'mobiphone',
			'email',
			'live_city',
			'address_city',
			'qq',
			'addresses',
			'member_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('member/social_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>