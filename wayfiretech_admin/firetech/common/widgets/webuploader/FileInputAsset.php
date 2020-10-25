<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-25 17:31:21
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-25 17:36:33
 */
 

// namespace manks;
namespace common\widgets\webuploader;

use yii\web\AssetBundle;

class FileInputAsset extends AssetBundle
{
    public $css = [
    	'webuploader/style.css',
        'webuploader/webuploader.css',
        'css/style.css',
    ];
    public $js = [
        'webuploader/webuploader.min.js',
        'webuploader/init.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__;
        parent::init();
    }
}
