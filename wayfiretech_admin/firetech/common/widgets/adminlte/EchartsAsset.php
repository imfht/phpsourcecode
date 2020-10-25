<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-03 12:29:49
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-13 16:46:02
 */

namespace common\widgets\adminlte;

use backend\assets\AppAsset;
use Exception;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class VueJsAsset.
 */
class EchartsAsset extends AssetBundle
{
    public $sourcePath = '@vue/';

    public $css = [
        // 'element-ui/lib/theme-chalk/index.css',
    ];

    public $js = [
        'src/echarts.js',
        'src/echartsThem.js',//默认主题
    ];

    public $jsOptions = [
        'charset'=>"utf-8",
        // 'type'=>'module',
        'position'=>View::POS_HEAD
    ];

    public $depends = [
        'common\widgets\adminlte\VueJsAsset'
    ];

    /**
     * @var string|bool Choose skin color, eg. `'skin-blue'` or set `false` to disable skin loading
     *
     * @see https://almsaeedstudio.com/themes/AdminLTE/documentation/index.html#layout
     */
    public $skin = 'default';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Append skin color file if specified
        if ($this->skin) {
            if (('default' !== $this->skin) && (strpos($this->skin, 'skin-') !== 0)) {
                throw new Exception('Invalid skin specified');
            }
            $this->css[] = sprintf('style/%s/index.css', trim($this->skin));
        }
        

        parent::init();
    }

    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile)
    {
        $basePath = \Yii::$app->assetManager->getPublishedUrl('@vue/');
        $view->registerJsFile($basePath.'/'.$jsfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile)
    {
        $basePath = \Yii::$app->assetManager->getPublishedUrl('@vue/');

        $view->registerCssFile($basePath.'/'.$cssfile, [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
    }
}
