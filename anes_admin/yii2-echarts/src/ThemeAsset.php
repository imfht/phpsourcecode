<?php
/**
 * @link https://github.com/anes/yii2-echarts
 * @copyright Copyright (c) 2016, Cosmo
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace anes\echarts;

use yii\web\AssetBundle;

/**
 * Theme asset
 *
 * @author Cosmo <52v1@163.com>
 */
class ThemeAsset extends AssetBundle
{
    public $sourcePath = '@bower/echarts/theme';

    public $depends = [
        'anes\echarts\EChartsAsset',
    ];
}