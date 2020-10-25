<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace fbi\xhprof;

use yii\web\AssetBundle;

/**
 * Debugger asset bundle
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class XhprofAsset extends AssetBundle
{
    public $sourcePath = '@fbi/xhprof/assets';
    public $css = [
        'css/xhprof.css',
		'jquery/jquery.tooltip.css',
        'jquery/jquery.autocomplete.css',
    ];
	public $js = [
		'jquery/jquery-1.2.6.js',
		'jquery/jquery.autocomplete.js',
		'jquery/jquery.tooltip.js',
		'js/xhprof_report.js',
	];
}
