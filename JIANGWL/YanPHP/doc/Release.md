## V0.2更新日志

#### 新增入参规则支持

|规则|参数|使用说明|例子|
|:---:|:---:|:---:|:---:|
|optional|否|参数可空||
|contain|是|入参是否包含给出的值|`contain(ab)`|
|in|是|入参是否等于给定的值的其中一个|`in([ab,123,cd])`|

#### 入参规则参数格式变更
为了支持新的数组入参规则，特意对此进行了修改

入参规则参数格式变更：

变更前：
```ini
[index]
user_id="starts_with[1]|regex[/[0-9]+/]|numeric"
```

变更后：
```ini
[index]
user_id="starts_with(1)|regex(/[0-9]+/)|numeric"
```

参数括号从`[]`更改为`()`


#### DB新增多连接支持
在配置文件`database.php`配置我们的连接后，可以实现多个db连接实例。

下面我们将介绍如何进行连接的切换。

`Model/User.php`
```php
<?php
namespace App\Cgi\Model;

use Illuminate\Support\Collection;
use Yan\Core\Model;

class User extends Model
{
    protected $table = 'user';
    protected $connection = 'mysql1';  //这里可以配置User Model默认使用"mysql1"连接

    public function getById($id): Collection
    {
        //这里可以使当前实例的连接切换为"default"
        $this->setConnection('default');
        
        return $this->where([$this->primaryKey => $id])->get();
    }
}
```

我们可以使用Model当中的`$connection`配置默认的连接。

另外一种方法是使用自带的`$this->setConnection($name)`方法进行连接的设置

#### 其他

修复了部分bug