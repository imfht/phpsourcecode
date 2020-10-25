<?php

namespace App\Console\Commands;

use App\Libraries\InfoHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watcher:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对Lara Watcher初始化安装';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = $this->ask('填入应用即将使用的URL地址？（不填默认为http://127.0.0.1:8000）');
        if (empty($url)) $url = 'http://127.0.0.1:8000';
        $db_host = $this->ask('填入数据库地址？（不填默认为127.0.0.1）');
        if (empty($db_host)) $db_host = '127.0.0.1';
        $db_port = $this->ask('填入数据库端口？（不填默认为3306）');
        if (empty($db_port)) $db_port = '3306';
        $db_database = $this->ask('填入数据库名称？');
        $db_username = $this->ask('填入数据库用户名？');
        $db_password = $this->ask('填入数据库密码？');
        $this->info('应用地址：' . $url);
        $this->info('数据库地址：' . $db_host);
        $this->info('数据库端口：' . $db_port);
        $this->info('数据库名称：' . $db_database);
        $this->info('数据库用户：' . $db_username);
        $this->info('数据库密码：' . $db_password);
        $check = $this->ask('请确认上述内容，无误输入"y"确认？');
        if ($check == 'y' || $check == 'Y') {
            if (!copy('.env.example', '.env')) {
                $this->error('配置文件创建失败！');
                return 0;
            }

            InfoHelper::setEnv([
                'APP_URL' => $url,
                'DB_HOST' => $db_host,
                'DB_PORT' => $db_port,
                'DB_DATABASE' => $db_database,
                'DB_USERNAME' => $db_username,
                'DB_PASSWORD' => $db_password
            ]);

            $this->info('正在优化配置！');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
//            $this->info('正在安装后台脚手架！');
//            Artisan::call('admin:publish');
            $this->info('正在生成数据库结构！');
            Artisan::call('migrate');
            $this->info('正在初始化数据！');
            DB::unprepared(file_get_contents(base_path('sql/initData.sql')));
            $this->info('正在设置存储系统！');
            Artisan::call('storage:link');
            $this->info('安装完成！请访问 ' . $url);
            $this->warn('用户名密码都为：admin');
            return 0;
        } else {
            $this->info('请重新执行 php artisan watcher:install 命令安装！');
            return 0;
        }
    }
}
