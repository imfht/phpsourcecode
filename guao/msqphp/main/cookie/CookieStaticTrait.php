<?php declare (strict_types = 1);
namespace msqphp\main\cookie;

trait CookieStaticTrait
{

    // 当前脚本所有的cookie
    private static $cookies = [];

    // 配置
    private static $config = [
        // 前缀
        'prefix'      => '',
        // 过期时间
        'expire'      => 3600,
        // 路径
        'path'        => '/',
        // 域名
        'domain'      => '',
        // https
        'secure'      => false,
        // httpoly
        'httponly'    => false,
        // 过滤
        'filter'      => false,
        // url转义
        'transcoding' => false,
        // 加密
        'encode'      => false,
    ];

    // 静态类初始化
    private static function initStatic(): void
    {
        // 初始化过直接返回
        static $inited = false;

        if ($inited) {
            return;
        }
        $inited = true;

        // 配置合并
        static::$config = $config = array_merge(static::$config, core\config\Config::get('cookie'));
        // 是否过滤cookie
        if ($config['filter']) {
            $prefix  = $config['prefix'];
            $len     = strlen($prefix);
            $_COOKIE = array_filter($_COOKIE, function (string $key) use ($len, $prefix): bool {
                // 如果以前缀开头,保留
                return 0 === strncmp($key, $prefix, $len);
            }, ARRAY_FILTER_USE_KEY);
        }
        static::$cookies = &$_COOKIE;
    }
}
