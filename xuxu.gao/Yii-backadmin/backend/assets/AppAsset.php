<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'public/vendors/animate-css/animate.min.css',
        'public/vendors/sweet-alert/sweet-alert.min.css',
        'public/vendors/material-icons/material-design-iconic-font.min.css',
        'public/vendors/socicon/socicon.min.css',
        'public/css/app.min.1.css',
        'public/css/app.min.2.css',
        'public/vendors/fullcalendar/fullcalendar.css',
        'public/vendors/select2/css/select2.min.css',
    ];
    public $js = [
        'public/js/jquery-2.1.1.min.js',
        'public/js/bootstrap.min.js',
        'public/vendors/flot/jquery.flot.min.js',
        'public/vendors/flot/jquery.flot.resize.min.js',
        'public/vendors/flot/plugins/curvedLines.js',
        'public/vendors/sparklines/jquery.sparkline.min.js',
        'public/vendors/easypiechart/jquery.easypiechart.min.js',
        'public/vendors/fullcalendar/lib/moment.min.js',
        'public/vendors/fullcalendar/fullcalendar.min.js',
        'public/vendors/simpleWeather/jquery.simpleWeather.min.js',
        'public/vendors/auto-size/jquery.autosize.min.js',
        'public/vendors/nicescroll/jquery.nicescroll.min.js',
        'public/vendors/waves/waves.min.js',
        'public/vendors/bootstrap-growl/bootstrap-growl.min.js',
        'public/vendors/sweet-alert/sweet-alert.min.js',
        'public/js/flot-charts/curved-line-chart.js',
        'public/js/flot-charts/line-chart.js',
        'public/js/charts.js',
        'public/js/demo.js',
        'public/js/functions.js',
        'public/vendors/waves/waves.min.js',
        'public/js/functions.js',
        'public/vendors/layer/layer.js',
        'public/vendors/select2/js/select2.full.min.js'
    ];
    public $depends = [

    ];
    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile($jsfile, ['depends'=>['backend\assets\AppAsset']]);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile($cssfile, ['depends'=>['backend\assets\AppAsset']]);
    }
}
