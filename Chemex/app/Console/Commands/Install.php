<?php

namespace App\Console\Commands;

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
    protected $signature = 'chemex:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对Chemex初始化安装';

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
        $this->info('正在优化配置！');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        $this->info('正在设置存储系统！');
        Artisan::call('storage:link');
        $this->info('正在生成表结构！');
        Artisan::call('migrate');
        $this->info('正在初始化数据！');
        DB::unprepared(file_get_contents(base_path('database/scripts/initDB.sql')));
        $this->info('安装完成！');
        $this->warn('用户名密码都为：admin');
        return 0;
    }
}
