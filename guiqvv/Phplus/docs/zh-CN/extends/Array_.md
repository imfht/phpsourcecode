# Array

## 引入类文件
在文件顶部加入下面的代码
``` php
use zendforum\Phplus\Array_;
```

## 使用
1、计算数组的维度
``` php
Array_::dimension(['a',['b'],[['c']]]);
返回值:3
```

2、数组生成器
``` php
Array_::generator(2, 'a');
返回值:
array(2) {
    [0]=>"a"
    [1]=>"a"
}
```

``` php
Array_::generator(2, 'a', 2);
返回值:
array(2) {
    [0]=>array(2) {
        [0]=>"a"
        [1]=>"a"
    }
    [1]=>array(2) {
        [0]=>"a"
        [1]=>"a"
    }
}
```
