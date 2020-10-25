<?php

namespace Lxj\Yii2\Tars\Commands;

use Lxj\Yii2\Tars\Registries\Registry;
use Lxj\Yii2\Tars\Route\TarsRouteFactory;
use Lxj\Yii2\Tars\Util;
use Tars\cmd\Command as TarsCommand;
use Tars\route\RouteFactory;
use \yii\console\Controller;

class TarsController extends Controller
{
    public function actionDeploy()
    {
        \Tars\deploy\Deploy::run();
    }

    public function actionEntry($cmd, $cfg)
    {
        class_alias(TarsRouteFactory::class, RouteFactory::class);

        list($hostname, $port, $appName, $serverName) = Util::parseTarsConfig($cfg);

        Util::app()->params['tars']['deploy_cfg'] = $cfg;

        Registry::register($hostname, $port);

        $class = new TarsCommand($cmd, $cfg);
        $class->run();
    }

    public function actionPublish()
    {
        $basePath = Util::app()->getBasePath();
        $tarsServantDir = $basePath . '/tars/servant';
        $tarsServantImplDir = $basePath . '/tars/impl';
        $tarsCservantDir = $basePath . '/tars/cservant';

        $this->ensureDirExisted($tarsServantDir);
        $this->ensureDirExisted($tarsServantImplDir);
        $this->ensureDirExisted($tarsCservantDir);

        $this->publishResource([
            __DIR__ . '/../index.php' => $basePath . '/index.php',
            __DIR__ . '/../Tars/cservant/.gitkeep' => $tarsCservantDir . '/.gitkeep',
            __DIR__ . '/../services.php' => $basePath . '/services.php',
            __DIR__ . '/../../tars/tars.proto.php' => $basePath . '/../tars/tars.proto.php',
            __DIR__ . '/../../scripts/tars2php.sh' => $basePath . '/../scripts/tars2php.sh',
            __DIR__ . '/../Tars/servant/.gitkeep' => $tarsServantDir . '/.gitkeep',
            __DIR__ . '/../Tars/impl/.gitkeep' => $tarsServantImplDir . '/.gitkeep',
        ]);

        $this->publishConfig([
            __DIR__ . '/../config/tars.php' => $basePath . '/config/params.php',
        ]);

        $this->stdout('Published.' . PHP_EOL);
    }

    private function publishResource($resources)
    {
        foreach ($resources as $src => $dest) {
            $this->ensureDirExisted(dirname($dest));
            copy($src, $dest);
        }
    }

    private function publishConfig($config)
    {
        foreach ($config as $src => $dest) {
            $destConfig = include $dest;
            $srcConfig = include $src;
            $destConfig['tars'] = array_merge($srcConfig, isset($destConfig['tars']) ? $destConfig['tars'] : []);
            file_put_contents($dest, '<?php' . PHP_EOL . 'return ' . var_export($destConfig, true) . ';' . PHP_EOL);
        }
    }

    private function ensureDirExisted($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
