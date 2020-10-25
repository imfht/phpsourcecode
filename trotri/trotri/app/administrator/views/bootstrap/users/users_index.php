<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getLoginNameLink($data)
	{
		return $this->elements_object->getLoginNameLink($data);
	}

	public function getValidMail($data)
	{
		$params = array(
			'id' => $data['user_id'],
			'column_name' => 'valid_mail'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['user_id'],
			'name' => 'valid_mail',
			'value' => $data['valid_mail'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getValidPhone($data)
	{
		$params = array(
			'id' => $data['user_id'],
			'column_name' => 'valid_phone'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['user_id'],
			'name' => 'valid_phone',
			'value' => $data['valid_phone'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getForbidden($data)
	{
		$params = array(
			'id' => $data['user_id'],
			'column_name' => 'forbidden'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['user_id'],
			'name' => 'forbidden',
			'value' => $data['forbidden'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getIpRegistered($data)
	{
		$output = long2ip($data['ip_registered']);
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['user_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$trashIcon = $this->getTrashIcon($params);

		$output = $modifyIcon . $trashIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('users/users_index_btns'); ?>

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

<?php $this->display('users/users_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>