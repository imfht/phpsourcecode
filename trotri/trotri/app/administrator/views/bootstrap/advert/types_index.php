<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getTypeNameLink($data)
	{
		return $this->elements_object->getTypeNameLink($data);
	}

	public function getPicture($data)
	{
		$imgUrl = $this->view->static_url . '/images/advtypes/';
		$imgExt = '.gif';

		$pictureLang = $this->elements_object->getPictureLangByPicture($data['picture']);
		$output = $this->html->img($imgUrl . $data['picture'] . $imgExt, $pictureLang, array('title' => $pictureLang));
		return $output;
	}

	public function getAdvertCount($data)
	{
		return $this->elements_object->getAdvertCount($data['type_key']);
	}

	public function getAdverts($data)
	{
		$params = array('type_key' => $data['type_key']);

		$indexIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_INDEX,
			'url' => $this->urlManager->getUrl('index', 'adverts', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_ADVERT_URLS_ADVERTS_INDEX,
		));

		$createIcon = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_CREATE,
			'url' => $this->urlManager->getUrl('create', 'adverts', $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->MOD_ADVERT_URLS_ADVERTS_CREATE,
		));

		// $output = $indexIcon . $createIcon;
		$output = $indexIcon;
		return $output;
	}

	public function getOperate($data)
	{
		$params = array(
			'id' => $data['type_id'],
		);

		$modifyIcon = $this->getModifyIcon($params);
		$removeIcon = $this->getRemoveIcon($params);

		$output = $modifyIcon . $removeIcon;
		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php $this->display('advert/types_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'type_name' => array(
				'callback' => 'getTypeNameLink'
			),
			'picture' => array(
				'callback' => 'getPicture'
			),
			'advert_count' => array(
				'callback' => 'getAdvertCount'
			),
			'adverts' => array(
				'callback' => 'getAdverts'
			),
		),
		'columns' => array(
			'type_name',
			'type_key',
			'picture',
			'description',
			'advert_count',
			'adverts',
			'type_id',
			'_operate_',
		),
	)
);
?>

<?php $this->display('advert/types_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	$this->paginator
);
?>