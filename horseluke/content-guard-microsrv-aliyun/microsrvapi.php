<?php

use SCH60\Kernel\App;

require __DIR__. '/protected/ThirdParty/SCH60Framework/Kernel.php';

//======================程序运行区================

define("D_DEBUG", 1);
define("D_ENV", 'Dev');
define("D_APP_DIR", __DIR__. '/protected/App');
define('D_ENTRY_FILE', __FILE__);
define('D_CONTROLLER_NAME', 'ControllerMicroServiceApi');
define('D_DISABLE_SESSION', true);

$app = new App();
$app->addLoadClassPath('ThirdPartyLoadByPsr4', __DIR__. '/protected/ThirdPartyLoadByPsr4');
$app->run();
