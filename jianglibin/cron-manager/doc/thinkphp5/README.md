# 如何在thinkphp5中使用cron-manager


## 自定义命令行

> 对自定义命令行感兴趣的可以去看 [thinkphp5官方手册](https://www.kancloud.cn/manual/thinkphp5/235129)


第一步,安装最新的cron-manager
```
composer require godv/cron-manager
```

第二步, 配置TP5项目的 `application/command.php` 文件

```php
<?php
return [
    'app\cron\command\Cron',
];
```

第三步, 创建cron命令文件, 没有就手动创建  `application/cron/Cron.php` 

```php
<?php
namespace app\cron\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

use think\console\input\Argument;
use think\console\input\Option;

class Cron extends Command
{
    protected function configure()
    {
        $this->addArgument('param', Argument::OPTIONAL);//查看状态
        // 设置命令名称
        $this->setName('cron')->setDescription('this is a supercron!');
    }

    protected function execute(Input $input, Output $output)
    {
        
        //获取参数值
        $args = $input->getArguments();
       
        $manager = new \SuperCronManager\CronManager();
        // 守护进程方式启动
        $manager->daemon = true;
        $manager->argv = $args['param'];

        // crontab格式解析
        $manager->taskInterval('每个小时的1,3,5分钟时运行一次', '1,3,5 * * * *', function(){
            echo "每个小时的1,3,5分钟时运行一次\n";
        });

        $manager->taskInterval('每1分钟运行一次', '*/1 * * * *', function(){
            echo "每1分钟运行一次\n";
        });

        $manager->taskInterval('每天凌晨运行', '0 0 * * *', function(){
            echo "每天凌晨运行\n";
        });

        $manager->taskInterval('每秒运行一次', 's@1', function(){
            echo "每秒运行一次\n";
        });

        $manager->taskInterval('每分钟运行一次', 'i@1', function(){
            echo "每分钟运行一次\n";
        });

        $manager->taskInterval('每小时钟运行一次', 'h@1', function(){
            echo "每小时运行一次\n";
        });

        $manager->taskInterval('指定每天00:00点运行', 'at@00:00', function(){
            echo "指定每天00:00点运行\n";
        });

        $manager->run();
    }
}
```


## 大功告成,开始使用

### 运行 (`进入tp5根目录`)

```
php think cron 
```

命令列表

> php think cron stop 停止|restart 重启|status 任务状态|worker 进程状态|check 检查环境