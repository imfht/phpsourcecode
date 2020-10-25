<?php
/**
 * @link https://github.com/anes/yii2-echarts
 * @copyright Copyright (c) 2016, Cosmo
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace anes\echarts;

use yii\web\AssetBundle;

/**
 * EChartsGl asset
 *
 * @author Cosmo <52v1@163.com>
 */
class GlAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@backend/web/css/js';
    
    /**
     * Initializes the bundle.
     */
    public function init()
    {       
        if (empty($this->js)) {
            $this->js = YII_DEBUG ? ['echarts-gl.js'] : ['echarts-gl.min.js'];
        }
        
        parent::init();
    }
    
}