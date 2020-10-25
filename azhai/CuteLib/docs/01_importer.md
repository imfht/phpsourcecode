
## Importer 寻找并自动加载class

可以自动加载符合PSR-0标准的类，作用与Symfony中的Class Loader相同。

在我常用的一些PHP库中测试都没有问题，包括

* ApnsPHP
* Assetic
* ConsoleKit
* Gregwar\Captcha
* Guzzle
* NotORM
* Requests
* SwiftMailer
* Symfony
* Twig
* Yaml
* Zf/Zf2

它们中既有如Zf2一样使用namespace的，也有Zf一样使用_分割的长类名。

其中不少自带Autoload，或者使用composer，但您完全可以用\Cute\Importer代替它们。

```php
defined('CUTE_ROOT') or define('CUTE_ROOT', __DIR__);
defined('SRC_ROOT') or define('SRC_ROOT', CUTE_ROOT . '/src');
require_once SRC_ROOT . '/Cute/Importer.php';

$importer = \Cute\Importer::getInstance();
//注册namespace前缀所在目录
$importer->addNamespace('Assetic', VENDOR_DIR . '/Assetic/src'); //PSR-0标准
//等价方式，区别在于目录最后一段是否包名
$importer->addNamespaceStrip('Assetic', VENDOR_ROOT . '/Assetic/src/Assetic');
//多段前缀，将优先（贪婪）匹配最长的一个
$importer->addNamespace('Gregwar\\Captcha', VENDOR_ROOT); //多段前缀
//或者注册类入口的文件
$locator->addClass(VENDOR_ROOT . '/NotORM/NotORM.php',
    'NotORM', 'NotORM_Result', 'NotORM_Row', 'NotORM_Literal', 'NotORM_Structure');
```
