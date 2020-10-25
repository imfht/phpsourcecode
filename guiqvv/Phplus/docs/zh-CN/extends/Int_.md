# Int_

## 引入类文件
在文件顶部加入下面的代码
``` php
use zendforum\Phplus\Int_;
```

## 使用
1、Int_::is_id
``` php
Int_::is_id($param);
返回值:bool
说明:判断是否为数据库id数据。
```

2、Int_::max
``` php
Int_::max();
返回值:9223372036854775807;//64位系统
OR
返回值:2147483647;//32位系统
说明:获取当前系统int型整数的最大值,大于此值即为float型。
```

3、Int_::size
``` php
Int_::size();
返回值:8;//64位系统
OR
返回值:4;//32位系统
说明:获取当前系统int型的字长。
```
