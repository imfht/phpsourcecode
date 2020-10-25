\Pram\Locator
==============

A class loader for PSR-0-style class loading. Same as Symfony Class Loader.

一个用于PHP自动加载的类，作用与Symfony中的Class Loader相同。

在我常用的一些PHP库中测试都没有问题，包括

* ApnsPHP
* ConsoleKit
* Guzzle
* NotORM
* React
* Requests
* SwiftMailer
* Symfony
* Twig
* Yaml
* Zf/Zf2

它们中既有如Zf2一样使用namespace的，也有Zf一样使用_分割的长类名。

其中不少自带Autoload，或者使用composer，但您完全可以用\Pram\Locator代替它们。


# 使用方法

```php
    <?php
    defined(VENDOR_ROOT) define(VENDOR_ROOT, __DIR__ . '/pram3/vendor');
    //先将这个类包含进来，创建单例，同时它将注册到PHP系统中
    require_once __DIR__ . '/pram3/src/Pram/Locator.php';
    $locator = \Pram\Locator::getInstance();
    //注册namespace前缀所在目录
    $locator->addNamespace('NotORM', VENDOR_ROOT . '/NotORM');
    //或者注册类入口的文件
    $locator->addClass(VENDOR_ROOT . '/NotORM/NotORM.php',
        'NotORM', 'NotORM_Result', 'NotORM_Row', 'NotORM_Literal', 'NotORM_Structure');
    //复杂的用法，React就是这朵奇葩，把Promise当作自己的子Namespace使用
    $locator->addNamespace('React', array(
        '.' => VENDOR_ROOT . '/React/src',
        'Promise' => VENDOR_ROOT . '/Promise/src/React',
    ));
    $locator->addNamespace('Guzzle', VENDOR_ROOT . '/Guzzle/src');
    $locator->addNamespace('Evenement', VENDOR_ROOT . '/Evenement/src');
```

