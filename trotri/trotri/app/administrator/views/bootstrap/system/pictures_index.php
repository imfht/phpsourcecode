<?php
use views\bootstrap\components\ComponentsConstant;
use views\bootstrap\components\ComponentsBuilder;

class TableRender extends views\bootstrap\components\TableRender
{
	public function getPicturePreview($data)
	{
		$imgHtml = $this->html->image($data['picture_url'], '', array('width' => 100, 'height' => 100));
		return $this->html->a($imgHtml, $data['picture_url'], array('target' => '_blank'));
	}

	public function getOperate($data)
	{
		$output = ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_LINK,
			'jsfunc' => 'System.textCopy(\'' . $data['picture_url'] . '\');',
			'title' => $this->view->MOD_SYSTEM_SYSTEM_PICTURES_COPY_PICTURE_URL,
		));

		return $output;
	}
}

$tblRender = new TableRender($this->elements);
?>

<?php if (strlen($this->directory) <= 6) : ?>

<?php
$html = $this->getHtml();
$urlManager = $this->getUrlManager();
?>

<?php echo $html->openTag('div', array('class' => 'bs-glyphicons')); ?>
<?php echo $html->openTag('ul', array('class' => 'bs-glyphicons-list')); ?>

<?php foreach ($this->data as $row) : ?>

<?php echo $html->openTag('li'); ?>
<?php echo $html->tag('span', array('class' => 'glyphicon glyphicon-folder-close'), ''); ?>
<?php echo $html->tag('span', array('class' => 'glyphicon-class'), $row['directory']); ?>
<?php echo $html->tag('span', array('class' => 'glyphicon-class'), '( ' . $row['file_count'] . ' )'); ?>
<?php echo $html->a($this->CFG_SYSTEM_GLOBAL_VIEW, $urlManager->getUrl('index', $this->controller, $this->module, array('directory' => $row['directory']))); ?>
<?php echo $html->closeTag('li'); ?>

<?php endforeach; ?>

<?php echo $html->closeTag('ul'); ?>
<?php echo $html->closeTag('div'); ?>

<?php else : ?>

<?php $this->display('system/pictures_index_btns'); ?>

<?php
$this->widget(
	'views\bootstrap\widgets\TableBuilder',
	array(
		'data' => $this->data,
		'table_render' => $tblRender,
		'elements' => array(
			'picture_preview' => array(
				'callback' => 'getPicturePreview'
			),
		),
		'columns' => array(
			'picture_preview',
			'picture_name',
			'picture_url',
			'file_size',
			'width_height',
			'dt_created',
			'_operate_',
		),
		'checkedToggle' => '',
	)
);
?>

<?php $this->display('system/pictures_index_btns'); ?>

<?php endif; ?>