<?php

namespace Lxj\Yii2\Tars;

use Lxj\Yii2\Tars\Commands\TarsController;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 *
 * @author Roy <luoxiaojun1992@sina.cn>
 * @since 2.0
 */
class Yii2Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $app->controllerMap['tars'] = TarsController::class;
        }

        \Yii::setAlias('@app/tars', $app->getBasePath() . '/tars');
    }
}
