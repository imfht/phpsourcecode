<?php

namespace Lxj\Yii2\Tars;

use Tars\Utils;
use yii\web\Application;

class Util
{
    protected static $app;

    public static function parseTarsConfig($cfg)
    {
        $hostname = gethostname();
        $tarsConfig = Utils::parseFile($cfg);
        $tarsServerConf = $tarsConfig['tars']['application']['server'];
        $port = $tarsServerConf['listen'][0]['iPort'];
        $appName = $tarsServerConf['app'];
        $serverName = $tarsServerConf['server'];
        return [$hostname, $port, $appName, $serverName];
    }

    /**
     * @return Application
     * @throws \yii\base\InvalidConfigException
     */
    public static function app()
    {
        if (!is_null(self::$app)) {
            return self::$app;
        }

        return self::$app = new Application(include \Yii::$app->getBasePath() . '/config/web.php');
    }
}
