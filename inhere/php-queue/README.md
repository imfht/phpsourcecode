# php 的队列实现

php的队列使用包装, 默认自带支持 `高 high` `中 norm` `低 low` 三个级别的队列操作。

- `DbQueue` 基于数据库(mysql/sqlite)的队列实现
- `PhpQueue` 基于 php `SplQueue` 实现
- `RedisQueue` 基于 redis 实现 - 操作具有原子性,并发操作不会有问题
- `ShmQueue` 基于共享内存实现  - 操作会自动加锁,并发操作不会有问题
- `SysVQueue` 基于 *nix 系统的 system v message 实现. php 需启用 `--enable-sysvmsg` 通常是默认开启的 :)

## 安装

- composer

```json
{
    "require": {
        "inhere/queue": "dev-master"
    }
}
```

- 直接拉取

```bash
git clone https://gitee.com/inhere/php-queue.git // gitee
git clone https://github.com/inhere/php-queue.git // github
```

## 使用

```php
// file: example/queue.php
use Inhere\Queue\QueueInterface;

// require __DIR__ . '/autoload.php';

$q = \Inhere\Queue\Queue::make([
    'driver' => 'sysv', // shm sysv php
    'id' => 12,
]);
//var_dump($q);

$q->push('n1');
$q->push('n2');
$q->push(['n3-array-value']);
$q->push('h1', QueueInterface::PRIORITY_HIGH);
$q->push('l1', QueueInterface::PRIORITY_LOW);
$q->push('n4');

$i = 6;

while ($i--) {
    var_dump($q->pop());
    usleep(50000);
}
```

run `php example/queue.php`. output:

```
% php example/queue.php                                                                                                                                                     17-06-11 - 22:36:01
driver is sysv
string(2) "h1"
string(2) "n1"
string(2) "n2"
array(1) {
  [0] =>
  string(11) "n3-array-value"
}
string(2) "n4"
string(2) "l1"
```

## 其他

system v 内存查看：

```bash
ipcs
ipcs -a // 命令可以查看当前使用的共享内存、消息队列及信号量所有信息
ipcs -p // 命令可以得到与共享内存、消息队列相关进程之间的消息
ipcs -u // 命令可以查看各个资源的使用总结信息
ipcs -q // 只查看消息队列
ipcs -qa // 查看消息队列，并显示更多信息
```

删除共享内存：

```bash
$ ipcrm

usage: ipcrm [-q msqid] [-m shmid] [-s semid]
             [-Q msgkey] [-M shmkey] [-S semkey] ...
```

## License

MIT
