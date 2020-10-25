# MQ队列

PhalApi可以轻松与各MQ队列进行整合使用。  

## Gearman整合

下面以Gearman队列为例，讲解如何进行整合、开发和使用。  

### 在Api层写入队列

通常，是在Api层把需要处理的数据，写入到队列。例如：   
```php
<?php
namespace App\Api;
use PhalApi\Api;

/**
 * 计划任务
 */
class AppTask extends Api {
    public function push() {
        $rs = ['err_code' => 0, 'err_msg' => ''];

        // 在你需要的地方，写入队列
        $data = [
            'user_id' => 1,
            'time' => time(),
        ];
        $gmclient= new \GearmanClient();
        $gmclient->addServer();
        $gmclient->doBackground("after_app_task_push", json_encode($data));

        return $rs;
    }
}
```

### 在bin目录里编写MQ消费脚本

接下来，切换到后台CLI模式，在bin目录下编写MQ消费脚本。 

例如：```./bin/gearman_client_after_app_task_push.php```脚本代码如下：  

```php
<?php
/**
 * MQ任务
 */
require_once dirname(__FILE__) . '/../public/init.php';

// 创建对象
$gmworker= new GearmanWorker();

// 添加服务
$gmworker->addServer();

// 注册回调函数
$gmworker->addFunction("after_app_task_push", "gearman_client_after_app_task_push");

print "开始等待队列……\n";
while($gmworker->work())
{
    if ($gmworker->returnCode() != GEARMAN_SUCCESS)
    {
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}

// 编写你的回调处理函数
function gearman_client_after_app_task_push($job)
{
    // 获取提交数据
    $workload= $job->workload();
    $workload = json_decode($workload, true);

    $user_id = $workload['user_id'];

    echo "user_id: {$user_id} ...\n";

    return true;
}
```

### 执行

编写完成后，可直接执行，以便测试。  

```
php ./bin/gearman_client_after_app_task_push.php
```

测试通过没问题后，便可放到后台执行。使用```nohub```命名：  
```
nohub php /path/to/phalapi/bin/gearman_client_after_app_task_push.php >> /path/to/phalapi/bin/gearman_client_after_app_task_push.log 2>&1 &
```

### 守护进程与停止脚本

可以加一个守护进程的脚本```./bin/gearman_client_deamon.sh```：   
```bash
#!/bin/bash
# gearman守护进程

# 当前数量
cur_client_num=`ps -ef| grep gearman_client_after_app_task_push.php |grep -v grep|wc -l`

# 最大数量
MAX_CLIENT_NUM=20

source /etc/environment

for((i=$cur_client_num;i<$MAX_CLIENT_NUM;i++));
do
    nohub php /path/to/phalapi/bin/gearman_client_after_app_task_push.php >> /path/to/phalapi/bin/gearman_client_after_app_task_push.log 2>&1 &
done
```

再加一个停止的脚本```./bin/gearman_client_deamon.sh```：  
```bash
#!/bin/bash

kill `ps -ef| grep gearman_client_after_app_task_push.php |grep -v grep | awk '{print $2}'`
```

## RabbitMQ整合

待补充。

## NSQ整合

待补充。
