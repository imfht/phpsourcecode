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

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['user_id'],
		);

		$restoreIcon = $this->getRestoreIcon($params);

		$output = $restoreIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('users/users_trashindex_btns'); ?>

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
			'forbidden' => array(
				'callback' => 'getForbidden'
			),
		),
		'columns' => array(
			'login_name',
			'user_name',
			'user_mail',
			'user_phone',
			'valid_mail',
			'valid_phone',
			'dt_registered',
			'ip_registered',
			'forbidden',
			'user_id',
			'_operate_',
		),
		'checkedToggle' => 'user_id',
	)
);
?>

<?php $this->display('users/users_trashindex_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>