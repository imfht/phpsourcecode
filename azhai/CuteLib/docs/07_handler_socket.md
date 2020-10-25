
## HandlerSocket  快速读写MySQL的插件

基于 kjdev/php-ext-handlersocketi

```php
//连接数据库，决定表名和字段列表
$hs = new \Cute\ORM\HandlerSocket('127.0.0.1', 9999);
$fields = ['id','username','score','modified_at','is_active'];
$hs->open('db_test', 't_users', $fields);
//插入一行数据
$now = date('Y-m-d H:i:s');
$row = [1,'ryan',60,$now,true];
$hs->insert(array_combine($fields, $row));
//更新
$hs->update([1, 'David', 80, $now, true], null, 1);
//删除，根据主键
$hs->delete(1);
//读取一行
$ryan = $hs->get(1); //按主键
$ryan = $hs->get('username', 'ryan'); //按索引
//读取多行
$ryan = $hs->all(null, '>=', 1, 3, 1); //WHERE id >= 1 LIMIT 1,3
$ryan = $hs->in('username', 'ryan', 'jane'); //WHERE username IN ('ryan', 'jane')
$ryan = $hs->in('username', ['ryan', 'jane']); //与上面的等价
```
