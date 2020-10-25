<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class ThemeAsset extends AssetBundle
{

    public $css = [
        'css/theme.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\AppAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

    public function init()
    {
        parent::init();
        if (isset(Yii::$app->view->theme->basePath)) {
            $this->sourcePath = Yii::$app->view->theme->basePath;
        }
    }

    /**
     * Registers this asset bundle with a view.
     * @param \yii\web\View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $js = <<<JS
            $('[data-toggle="tooltip"]').tooltip()
JS;

        $view->registerJs($js, View::POS_READY);

        return parent::register($view);
    }
}