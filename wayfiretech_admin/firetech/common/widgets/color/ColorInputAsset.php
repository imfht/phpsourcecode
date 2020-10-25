<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-28 09:44:01
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-28 09:44:55
 */

namespace common\widgets\color;

class ColorInputAsset extends \kartik\base\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setSourcePath(__DIR__.'/assets');
        $this->setupAssets('css', ['css/spectrum', 'css/spectrum-kv']);
        $this->setupAssets('js', ['js/spectrum', 'js/spectrum-kv']);
        parent::init();
    }
}
