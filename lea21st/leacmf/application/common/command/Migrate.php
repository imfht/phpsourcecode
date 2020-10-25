<?php

namespace app\common\command;

use app\v1\model\User;
use Hashids\Hashids;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Migrate extends Command
{

    //命令描述
    protected function configure()
    {
        $this->setName('migrate')->setDescription('cancel the goods activity');
    }

    //所要执行的命令
    protected function execute(Input $input, Output $output)
    {

        $output->writeln("数据迁移完成");
    }
}