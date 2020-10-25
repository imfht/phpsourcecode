<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-25 16:58:25
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-25 16:58:27
 */
 
 
namespace common\widgets\ueditor;


use yii\web\AssetBundle;

class UEditorAsset extends AssetBundle
{
    public $js = [
        'ueditor.config.js',
        'ueditor.all.js',
    ];
   
    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    }
}