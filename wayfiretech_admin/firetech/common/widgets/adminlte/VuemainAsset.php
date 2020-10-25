<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-03 12:29:49
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-10 16:20:55
 */

namespace common\widgets\adminlte;

use backend\assets\AppAsset;
use Exception;
use yii\web\AssetBundle;

/**
 * Class VueJsAsset.
 */
class VuemainAsset extends AssetBundle
{
    public $sourcePath = '@vue/';

    public $css = [
        // 'element-ui/lib/theme-chalk/index.css',
    ];

    public $js = [
        // 'main.js',
    ];

    public $jsOptions = [
        'type'=>'module',
        'charset'=>"utf-8"
    ];

    public $depends = [
        'common\widgets\adminlte\VueJsAsset'
    ];
   
    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile)
    {
        $basePath = \Yii::$app->assetManager->getPublishedUrl('@vue/public/utli');
        $view->registerJsFile($basePath.'/'.$jsfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile)
    {
        $basePath = \Yii::$app->assetManager->getPublishedUrl('@vue/public/utli');

        $view->registerCssFile($basePath.'/'.$cssfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
    }
}
