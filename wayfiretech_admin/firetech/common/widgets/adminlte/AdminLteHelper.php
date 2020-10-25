<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-25 17:06:01
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-26 16:19:43
 */

namespace common\widgets\adminlte;

use Yii;

class AdminLteHelper
{
    /**
     * It allows you to get the name of the css class.
     * You can add the appropriate class to the body tag for dynamic change the template's appearance.
     * Note: Use this fucntion only if you override the skin through configuration.
     * Otherwise you will not get the correct css class of body.
     *
     * @return string
     */
    public static function skinClass()
    {
        /** @var \dmstr\web\AdminLteAsset $bundle */
        $bundle = Yii::$app->assetManager->getBundle('common\adminlte\AdminLteAsset');

        return $bundle->skin;
    }
}
