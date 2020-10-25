<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HssWhjAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'hss_whj/css/style.css',
        'hss_whj/css/banner.css',
        'hss_whj/xingkai/css/xingkai.css',
        'hss_whj/css/share.css',
    ];
    public $js = [
        'hss_whj/js/jquery.sgallery.js',
        // 'hss_whj/js/ad.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}
