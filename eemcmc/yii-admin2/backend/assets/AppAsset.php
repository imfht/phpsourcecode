<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'css/site.css',
		'zui/css/zui.css',
		'zui/lib/chosen/chosen.css',
		'zui/lib/datetimepicker/datetimepicker.css',
		'zui/lib/calendar/zui.calendar.css',
		'fileinput/css/fileinput.css',
	];
	public $js = [
		'zui/js/zui.js',
		'zui/lib/chosen/chosen.js', //选择插件
		'zui/lib/hotkey/hotkey.js', //键盘插件
		'zui/lib/datetimepicker/datetimepicker.js', //键盘插件
		'zui/lib/calendar/zui.calendar.js', //日历插件
		'js/baiduTemplate.js', //百度模板
		'fileinput/js/fileinput.js', //上传组件
		'js/backend.js', //类库
	];
	public $depends = [
		'yii\web\JqueryAsset',
//		'yii\bootstrap\BootstrapAsset',
	];

	/**
	 * @inheritdoc
	 * @param \yii\web\View $view 视图对象
	 */
	public function registerAssetFiles($view)
	{
		$time = time();
		$this->js[] = "actions/{$view->context->id}/{$view->context->action->id}.js?_={$time}";
		parent::registerAssetFiles($view);
	}

}
