<?php
class TableRender extends views\bootstrap\components\TableRender
{
	public function getLoginNameLink($data)
	{
		return $this->elements_object->getLoginNameLink($data);
	}

	public function getValidMail($data)
	{
		return $this->elements_object->getValidMailLangByValidMail($data['valid_mail']);
	}

	public function getValidPhone($data)
	{
		return $this->elements_object->getValidPhoneLangByValidPhone($data['valid_phone']);
	}

	public function getForbidden($data)
	{
		return $this->elements_object->getForbiddenLangByForbidden($data['forbidden']);
	}

	public function getIpRegistered($data)
	{
		$output = long2ip($data['ip_registered']);
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['member_id'],
		);

		$restoreIcon = $this->getRestoreIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $restoreIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/portal_trashindex_btns'); ?>

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
			'valid_mail' => array(
				'callback' => 'getValidMail'
			),
			'valid_phone' => array(
				'callback' => 'getValidPhone'
			),
			'ip_registered' => array(
				'callback' => 'getIpRegistered'
			),
			'forbidden' => array(
				'callback' => 'getForbidden'
			),
		),
		'columns' => array(
			'login_name',
			'member_name',
			'member_mail',
			'member_phone',
			'valid_mail',
			'valid_phone',
			'dt_registered',
			'ip_registered',
			'forbidden',
			'member_id',
			'_operate_',
		),
		'checkedToggle' => 'member_id',
	)
);
?>

<?php $this->display('member/portal_trashindex_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>