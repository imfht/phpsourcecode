
## Factory  对象工厂

可以根据配置文件生产或者缓存对象


```php
//Storage为配置容器，接受PHP配置文件作为参数
defined('APP_ROOT') or define('APP_ROOT', __DIR__);
$settings_file = APP_ROOT . '/settings.php';
$storage = new \Cute\Base\Storage(include $settings_file);
$factory = new \Cute\Base\Factory($storage);
//生成PDO对象，使用default参数组
$pdo = $factory->create('pdo', 'default');
//加载PDO对象，如果对象尚不存在，就会调用上面的方法先生成一个
$pdo = $factory->load('pdo', 'default');
```

配置文件的例子 settings.php

```php
<?php
defined('APP_ROOT') or die('Illeigal access'); // 禁止非法访问

return array(
    'app'    => [  //使用$app->getConfig()可以获取的参数
        'site_title' => 'Demo',
        'locale_dir' => APP_ROOT . '/locales',
        'route_key'  => 's',
        'url_prefix' => '/index.php',
        'asset_url'  => '/assets',
    ],
    'pdo'    => [
        'class'   => '\\PDO',
        'default' => [
            'dsn'      => 'mysql:host=127.0.0.1;port=3306;charset=utf8',
            'username' => 'dba',
            'password' => 'pass',
        ],
    ],
    'mysql'  => [
        'class'     => '\\Cute\\ORM\\Schema\\Mysql',
        'wordpress' => [
            '@pdo'   => 'default',
            'dbname' => 'db_wordpress',
            'tblpre' => 'wp_',
        ],
    ],
    'logger' => [
        'class'   => '\\Cute\\Log\\FileLogger',
        'sql'     => ['name' => 'sql', 'directory' => CUTE_ROOT . '/runtime/logs'],
    ],
);
```