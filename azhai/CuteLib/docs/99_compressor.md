
## Compressor 压缩PHP代码到一个文件

CuteLib可以将主要的代码压缩成一个文件，比如cute_base.php

因为PHP的工作方式是不驻留内存，每个进程执行完后，所占资源立即释放。

进程启动时，如果需要加载许多类文件，就会造成大量磁盘寻址操作，拖慢了执行速度。

所以Yii、Nette等PHP框架在启动时，都将几乎会用到的文件打包成一个。

反例是Druapl，其boostrap时大量加载文件，这个设计成了它执行慢的罪魁祸首，

比它的另一个糟糕设计（将钩子函数间的结构与顺序关系存储在数据库）拖慢得更多。

```
defined('CUTE_ROOT') or define('CUTE_ROOT', __DIR__);
defined('SRC_ROOT') or define('SRC_ROOT', CUTE_ROOT . '/src');
define('MINFILE', SRC_ROOT . '/cute_base.php');

//Importer.php文件压缩后，大小在1.5KB以上
if (! is_readable(MINFILE) || filesize(MINFILE) < 1024) {
    require_once SRC_ROOT . '/compressor.php';
    $compressor = new \Compressor();
    $compressor->prepend(SRC_ROOT . '/bootstrap.php');
    $compressor->minify(MINFILE,
            glob(SRC_ROOT . '/Cute/*.php'),
            glob(SRC_ROOT . '/Cute/Base/*.php'));
}
require_once MINFILE; // 使用压缩后的文件
```