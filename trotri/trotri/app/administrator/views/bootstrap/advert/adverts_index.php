<?php
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getAdvertNameLink($data)
	{
		return $this->elements_object->getAdvertNameLink($data);
	}

	public function getIsPublished($data)
	{
		$params = array(
			'id' => $data['advert_id'],
			'column_name' => 'is_published'
		);

		$output = ComponentsBuilder::getSwitch(array(
			'id' => $data['advert_id'],
			'name' => 'is_published',
			'value' => $data['is_published'],
			'href' => $this->urlManager->getUrl('singlemodify', $this->controller, $this->module, $params)
		));

		return $output;
	}

	public function getShowTypeLang($data)
	{
		$showTypeLang = $this->elements_object->getShowTypeLangByShowType($data['show_type']);
		$url = $this->urlManager->getUrl('preview', $this->controller, $this->module, array('id' => $data['advert_id']));
		$output = $showTypeLang . '&nbsp;' . $this->getPreviewIcon(array('url' => $url));
		return $output;
	}

	public function getSort($data)
	{
		return $this->html->text('sort[' . $data['advert_id'] . ']', $data['sort'], array('class' => 'form-control input-listsort', 'size' => '5'));
	}

	public function getDtCreated($data)
	{
		return date('Y-m-d', strtotime($data['dt_created']));
	}

	public function getShowCode($data)
	{
		return $this->html->tag('div', array('style' => 'width: 150px; height: 150px; overflow: hidden;'), $data['show_code']);
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['advert_id'],
			'type_key' => $data['type_key']
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php
echo $this->getHtml()->css(
	'video, img, embed { max-width: 100%; max-height: 100%; } a img + img { display: none; }'
);
?>

<?php $this->display('advert/adverts_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'advert_name' => array(
				'callback' => 'getAdvertNameLink'
			),
			'is_published' => array(
				'callback' => 'getIsPublished'
			),
			'show_type' => array(
				'callback' => 'getShowTypeLang'
			),
			'sort' => array(
				'callback' => 'getSort'
			),
			'dt_created' => array(
				'callback' => 'getDtCreated'
			),
			'show_code' => array(
				'callback' => 'getShowCode'
			),
		),
		'columns' => array(
			'advert_name',
			'show_code',
			'show_type',
			'is_published',
			'dt_publish_up',
			'dt_publish_down',
			'sort',
			'dt_created',
			'advert_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('advert/adverts_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>