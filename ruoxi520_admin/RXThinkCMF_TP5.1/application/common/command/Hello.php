<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use util\RabbitMQ;

/**
 * 定时任务脚本
 * @author 牧羊人
 * @date 2019/7/3
 * Class Hello
 * @package app\common\command
 */
class Hello extends Command
{
    /**
     * 指令配置
     * @author 牧羊人
     * @date 2019/7/3
     */
    protected function configure()
    {
        $this->setName('hello')
            ->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('Say Hello');
    }

    /**
     * 指令执行
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @author 牧羊人
     * @date 2019/7/3
     */
    protected function execute(Input $input, Output $output)
    {
//        $name = trim($input->getArgument('name'));
//        $name = $name ?: 'thinkphp';
//        if ($input->hasOption('city')) {
//            $city = PHP_EOL . 'From ' . $input->getOption('city');
//        } else {
//            $city = '';
//        }
//        $output->writeln("Hello," . $name . '!' . $city);
        $queue_name = "Test";
        // 加入队列
        $queue = new RabbitMQ($queue_name);
        $queue->put([
            'id' => 1,
            'name' => '云恒信息科技 ' . date('Y-m-d H:i:s', time()),
        ]);
        $queue->close();

        //拉取队列
        $queue = new RabbitMQ($queue_name);
        list($ack, $result) = $queue->get();
        $ack();
        print_r(json_decode($result, true));
        exit;
    }
}
