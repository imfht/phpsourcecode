<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Config;
use think\facade\Log;

class Backup extends Command
{

    /**
     * 过期时间，单位天，可以为小数
     * @var float
     */
    protected $deadline = 31;

    //命令描述
    protected function configure()
    {
        $this->setName('backup')->setDescription('Use this statement to back up a database and transaction log');
    }

    //所要执行的命令
    protected function execute(Input $input, Output $output)
    {
        //开始备份数据看
        $this->clear();
        $this->export();
        $output->writeln("数据库备份完成");
    }

    protected function getPath()
    {
        return env('root_path') . 'data/backup/';
    }

    /**
     * 备份数据库，温备份
     */
    protected function export()
    {
        $config = Config::get('database.');

        $cmd = '/usr/bin/mysqldump -h ' . $config['hostname'] . ' --user ' . $config['username'] . ' --password=' . $config['password'] . ' ' . $config['database'] . ' > ' . $this->getPath() . 'sql_backup_' . date('YmdHis') . '.sql;';
        try {
            passthru($cmd);
        } catch (\Exception $e) {
            Log::error($e->getCode() . $e->getMessage());
        }
        Log::write('数据库备份完成', 'info');
    }

    //删除早起的备份文件
    protected function clear()
    {
        $files = $this->getFiles();
        if ($files) {
            foreach ($files as $val) {
                $time = strtotime(substr($val, 11, 4) . '-' . substr($val, 15, 2) . '-' . substr($val, 17, 2) . ' ' . substr($val, 19, 2) . ':' . substr($val, 21, 2) . ':' . substr($val, 23, 2));
                if (time() - $time > $this->deadline * 24 * 60 * 60 && strlen($time) == 10) {
                    @unlink($this->getPath() . $val);
                    Log::info('删除数据库备份文件:' . $val, 'info');
                }
            }
        }
    }

    protected function getFiles()
    {
        $files   = [];
        $handler = opendir($this->getPath());
        while (($filename = readdir($handler)) !== false) {
            //3、目录下都会有两个文件，名字为’.'和‘..’，不要对他们进行操作
            if ($filename != "." && $filename != "..") {
                array_push($files, $filename);
            }
        }
        return $files;
    }
}