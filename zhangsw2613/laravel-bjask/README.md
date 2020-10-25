# laravel-bjask
使用swoole协程实现的laravel任务调度扩展包


## 安装

通过 Composer 安装

```bash
$ composer require zhangsw/laravel-bjask
```

## 使用方法

### 一、修改`config/app.php`配置文件
```php
    'providers' => [
        // ...
        Bjask\SchedulerServiceProvider::class,
    ]
```

```php
    'aliases' => [
        // ...
        'Scheduler' => Bjask\Facades\Scheduler::class,
    ]
```

### 二、创建目录：storage\framework\pid 修改目录权限可读写


### 三、创建app\Tasks目录（记得修改目录所属用户），并在目录下创建如下示例文件：
TestMessageTask.php
```php
namespace App\Tasks;

use Bjask\Task;
use Illuminate\Support\Facades\Log;
use Swoole\Coroutine;

class TestMessageTask extends Task
{
    public function prepare()
    {
        $this->everyMonth(1);
        $this->everyWeek(1);
        $this->everyDay(2);
        $this->everyHour(2);
        $this->everyMinute(1);
        $this->everySecond(3);
    }

    public function run(){
        Log::info(date('Y-m-d H:i:s',time()).'测试发送消息');
        Coroutine::sleep(3);
    }
}
```

### 四、执行命令
-启动：php artisan task:manage start

-关闭：php artisan task:manage stop

-重启：php artisan task:manage restart

-查看：php artisan task:manage status


## 说明
可配置调度如下：
* everyMonth 每隔几月
* everyWeek 每隔几周
* everyDay 每隔几天
* everyHour 每隔几小时
* everyMinute 每隔几分钟
* everySecond 每隔几秒钟




