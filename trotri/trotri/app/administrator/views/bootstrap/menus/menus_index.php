<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getMenuNameLink($data)
	{
		return $this->elements_object->getMenuNameLink($data);
	}

	public function getPicture($data)
	{
		if ($data['picture'] === '') {
			return '';
		}

		$imgHtml = $this->html->image($data['picture'], '', array('width' => 80, 'height' => 80));
		return $this->html->a($imgHtml, $data['picture'], array('target' => '_blank'));
	}

	public function getAllowUnregistered($data)
	{
		$params = array(
			'id' => $data['menu_id'],
			'column_name' => 'allow_unregistered'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['menu_id'],
			'name' => 'allow_unregistered',
			'value' => $data['allow_unregistered'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getIsHide($data)
	{
		$params = array(
			'id' => $data['menu_id'],
			'column_name' => 'is_hide'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['menu_id'],
			'name' => 'is_hide',
			'value' => $data['is_hide'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['menu_id'],
			'type_key' => $data['type_key']
		);

		$previewIcon = $this->getPreviewIcon(array('url' => $data['menu_url']));
		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $previewIcon . $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('menus/menus_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'menu_name' => array(
				'callback' => 'getMenuNameLink'
			),
			'picture' => array(
				'callback' => 'getPicture'
			),
			'allow_unregistered' => array(
				'callback' => 'getAllowUnregistered'
			),
			'is_hide' => array(
				'callback' => 'getIsHide'
			),
		),
		'columns' => array(
			'menu_name',
			'picture',
			'alias',
			'allow_unregistered',
			'is_hide',
			'sort',
			'dt_created',
			'menu_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('menus/menus_index_btns'); ?>
