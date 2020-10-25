# 介绍:

Redshift 是亚马逊AWS的一个高效的完全托管PB级别的数据仓库,能在数秒内在数TB数据中返回各种维度的group sum count等统计数据
这个是其驱动,原则上tp自带的PG驱动也可以,但使用过程报错,经简单修改发布这个版本的驱动.原则上也兼容postgre数据库.

安装方式:
```
composer require gclinux/think-redshift
```


复制代码
使用方式跟tp使用其他数据库一样,例如:


    <?php
    namespace app\common\model;
    use think\Model;
    class Redshift extends Model {
    	protected $connection = 'redshift';//这个跟config里面的配置项的key一致
    }

config.php 配置文增加 redishift

```
'redshift'=>[
  'type' => 'redshift',//重点
  // 数据库连接DSN配置
  'dsn' => '',
  // 服务器地址
  'hostname' => 'XXX.XXXX.redshift.amazonaws.com',
  // 数据库名
  'database' => 'redshift_db',
  // 数据库用户名
  'username' => 'joffe',
  // 数据库密码
  'password' => 'xxxxxxx',
  // 数据库连接端口
  'hostport' => '5439',
]
```

