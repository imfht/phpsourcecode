# Debug

## 引入类文件
在文件顶部加入下面的代码
``` php
use zendforum\Phplus\Debug;
```

## 使用
1、打印一
``` php
Debug::vd($param);
等同于:
var_dump($param);
```

2、打印二
``` php
Debug::vdd($param);
等同于:
var_dump($param);
die;
```

3、打印三
``` php
Debug::pr($param);
等同于:
print_r($param);
```
