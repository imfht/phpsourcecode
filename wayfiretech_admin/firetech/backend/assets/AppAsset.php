<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-08 00:25:30
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-08 00:26:45
 */
 
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:37
 * @LastEditTime: 2020-04-25 08:37:25
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'resource/css/site.css',
    ];
    public $js = [
        // 'assets/99a51ff8/jquery-ui.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'common\widgets\adminlte\AdminLteAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
