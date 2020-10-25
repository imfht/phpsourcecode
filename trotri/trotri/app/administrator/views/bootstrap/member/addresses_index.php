<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getAddressNameLink($data)
	{
		return $this->elements_object->getAddressNameLink($data);
	}

	public function getWhenLang($data)
	{
		return $this->elements_object->getWhenLangByWhen($data['when']);
	}

	public function getIsDefault($data)
	{
		$params = array(
			'id' => $data['address_id'],
			'column_name' => 'is_default'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['address_id'],
			'name' => 'is_default',
			'value' => $data['is_default'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['address_id'],
			'member_id' => $data['member_id']
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('member/addresses_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'address_name' => array(
				'callback' => 'getAddressNameLink'
			),
			'when' => array(
				'callback' => 'getWhenLang'
			),
			'is_default' => array(
				'callback' => 'getIsDefault'
			),
		),
		'columns' => array(
			'address_name',
			'consignee',
			'mobiphone',
			'telephone',
			'addr_province',
			'addr_city',
			'addr_district',
			'addr_street',
			// 'when',
			'is_default',
			'dt_last_modified',
			'address_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('member/addresses_index_btns'); ?>
