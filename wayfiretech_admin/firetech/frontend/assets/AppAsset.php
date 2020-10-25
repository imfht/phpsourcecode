<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-14 08:56:46
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-21 22:31:48
 */

/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:40
 * @LastEditTime: 2020-04-25 10:56:54
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 **/
class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/web/resource/';
    public $css = [
        // 'css/font-awesome.min.css',
        'css/style.css',
        'css/css-assets.css',
        // 'css/ionicons.min.css',
    ];

    public $js = [
        'js/jquery.js',
        'js/jRespond.min.js',
        'js/jquery.fitvids.js',
        'js/superfish.js',
        'scss/slick/slick.min.js',
        'js/jquery.magnific-popup.min.js',
        'js/scrollIt.min.js',
        'js/functions.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
