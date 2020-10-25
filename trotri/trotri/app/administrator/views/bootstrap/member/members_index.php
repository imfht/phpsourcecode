<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getLoginName($data)
	{
		$params = array(
			'id' => $data['member_id'],
		);

		$modifyIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_MODIFY,
			'url' => $this->urlManager->getUrl($this->actNameModify, $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_MEMBER_MEMBERS_MODIFY_P_PASSWORD,
		));

		$output = $data['login_name'] . '&nbsp;&nbsp;' . $modifyIcon;
		return $output;
	}

	public function getTypeName($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'column_name' => 'type_id'
		);

		$attributes = array(
			'class'    => 'form-control input-sm',
			'href'     => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params),
			'onchange' => 'return Core.changeSelectValue(this);'
		);

		$output = $this->html->openSelect('type_name', $attributes);
		$output .= $this->html->options($this->elements_object->getTypeNames(), $data['type_id']);
		$output .= $this->html->closeSelect();

		return $output;
	}

	public function getExperience($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'login_name' => $data['login_name'],
			'column_name' => 'experience',
			'value' => $data['experience'],
		);

		$increaseIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'increase')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_INCREASE_BALANCE,
		));

		$reduceIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => 'minus-sign',
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'reduce')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_REDUCE_BALANCE,
		));

		$output = $data['experience'] . '&nbsp;&nbsp;' . $increaseIcon . $reduceIcon;
		return $output;
	}

	public function getBalance($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'login_name' => $data['login_name'],
			'column_name' => 'balance',
			'value' => $data['balance'],
		);

		$increaseIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'increase')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_INCREASE_BALANCE,
		));

		$reduceIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => 'minus-sign',
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'reduce')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_REDUCE_BALANCE,
		));

		$output = $data['balance'] . '&nbsp;&nbsp;' . $increaseIcon . $reduceIcon;
		return $output;
	}

	public function getBalanceFreeze($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'login_name' => $data['login_name'],
			'column_name' => 'balance',
			'value' => $data['balance'] . ' ,  ' . $data['balance_freeze'],
		);

		$freezeIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'freeze')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_FREEZE_BALANCE,
		));

		$unfreezeIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => 'minus-sign',
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'unfreeze')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_UNFREEZE_BALANCE,
		));

		$output = $data['balance_freeze'] . '&nbsp;&nbsp;' . $freezeIcon . $unfreezeIcon;
		return $output;
	}

	public function getPoints($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'login_name' => $data['login_name'],
			'column_name' => 'points',
			'value' => $data['points'],
		);

		$increaseIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'increase')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_INCREASE_BALANCE,
		));

		$reduceIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => 'minus-sign',
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'reduce')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_REDUCE_BALANCE,
		));

		$output = $data['points'] . '&nbsp;&nbsp;' . $increaseIcon . $reduceIcon;
		return $output;
	}

	public function getPointsFreeze($data)
	{
		$params = array(
			'id' => $data['member_id'],
			'login_name' => $data['login_name'],
			'column_name' => 'points',
			'value' => $data['points'] . ' ,  ' . $data['points_freeze'],
		);

		$freezeIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'freeze')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_FREEZE_BALANCE,
		));

		$unfreezeIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => 'minus-sign',
			'url' => $this->urlManager->getUrl('ajaxaccount', $this->controller, $this->module, $params + array('op_type' => 'unfreeze')),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGAJAXVIEW,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_UNFREEZE_BALANCE,
		));

		$output = $data['points_freeze'] . '&nbsp;&nbsp;' . $freezeIcon . $unfreezeIcon;
		return $output;
	}

	public function getRankName($data)
	{
		return $this->elements_object->getRankNameByRankId($data['rank_id']);
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/members_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'login_name' => array(
				'callback' => 'getLoginName'
			),
			'type_id' => array(
				'callback' => 'getTypeName'
			),
			'rank_id' => array(
				'callback' => 'getRankName'
			),
			'experience' => array(
				'callback' => 'getExperience'
			),
			'balance' => array(
				'callback' => 'getBalance'
			),
			'balance_freeze' => array(
				'callback' => 'getBalanceFreeze'
			),
			'points' => array(
				'callback' => 'getPoints'
			),
			'points_freeze' => array(
				'callback' => 'getPointsFreeze'
			),
		),
		'columns' => array(
			'login_name',
			'type_id',
			'rank_id',
			'experience',
			'balance',
			'balance_freeze',
			'points',
			'points_freeze',
			'consum',
			'orders',
			'dt_last_rerank',
			'member_id',
		),
	)
);
?>

<?php $this->display('member/members_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>