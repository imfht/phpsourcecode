<?php

/**
 * 定义 QeePHP 框架的默认设定
 */

return array(
    'defaults.default_action' => 'index',
    'defaults.action_accessor' => 'action',
    'defaults.timezone' => 'Asia/Chongqing',

    'defaults.autoload_tools' => array(),
    'defaults.session_autostart' => false,

    'app.error' => array(

        'level' => E_ALL | E_STRICT,
        'exception' => 'qeephp\\error\\Handler::exception',
        'userlevel' => 'qeephp\\error\\Handler::userlevel',
        'fatal'     => 'qeephp\\error\\Handler::fatal',

    ),

    # 缺省缓存服务
    'cache.domains.default' => 'qeephp\\cache\\Memory',

);

