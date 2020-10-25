
## Application 应用程序

应用程序Application有两个子类Website和Console，分别对应Web和命令行

可在Application上安装插件，默认安装了Storage->getConfig()和Factory->load()

```php
//Storage为配置容器，接受PHP配置文件作为参数
defined('APP_ROOT') or define('APP_ROOT', __DIR__);
$settings = new \Cute\Cache\FileCache('settings', APP_ROOT);
$app = new \Cute\Web\Site($settings->readData());
//子类Website在初始化时，安装了Router和Input，并注册了若干方法
/*
$root = \Cute\Web\Router::getCurrent();
$app->installRef($root, ['dispatch', 'abort', 'redirect']);
$app->installRef(\Cute\Web\Router::$current, ['route', 'expose']);
//使用别名，Input的静态方法getInstance()变成$app的实例方法input()
$app->install('\\Cute\\Web\\Input', [
    'getClientIP', 'input' => 'getInstance',
]);
*/
```

