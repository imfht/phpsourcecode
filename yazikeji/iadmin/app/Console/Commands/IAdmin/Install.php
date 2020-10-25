<?php

namespace App\Console\Commands\IAdmin;

use App\Models\SysAdmins;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iadmin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安装系统';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $admin;
    public function __construct(SysAdmins $admin)
    {
        parent::__construct();
        $this->admin = $admin;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->ask('请输入管理员邮箱账号:', 'admin@admin.com');

        $nickname = $this->ask('请输入管理员昵称:', '超级管理员');

        $password = $this->secret('请输入账号密码:');

        //创建数据库
        $this->call('migrate');
        //创建数据
        $this->call('db:seed', ['--class'=>'BaseDataSeeder']);

        //插入管理员数据
        $admin = $this->admin->create([
            'email' => $email,
            'nickname' => $nickname,
            'password' => bcrypt($password),
            'active' => 1,
            'remember_token' => ''
        ]);
        $admin->roles()->attach(1);

        $this->info('数据库安装成功!');
    }
}
