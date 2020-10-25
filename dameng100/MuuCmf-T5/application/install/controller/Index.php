<?php
namespace app\install\controller;

use think\Controller;
use think\Config;

class Index extends Controller
{
    //安装首页
    public function index(){

        if (is_file(ROOT_PATH . 'install.lock'))
        {
            // 已经安装过了 执行更新程序
            $msg = '请删除install.lock文件后再运行安装程序!';
            $this->error($msg);
        }

        return view();
    }

    //安装完成
    public function complete(){
        clearstatcache();
        
        // 写入安装锁定文件
        $lockFile = ROOT_PATH .'install.lock';
        $result = @file_put_contents($lockFile, 'lock');
        //创建配置文件
        $this->assign('info',session('config_file'));
        session('step', null);
        session('error', null);
        session('update',null);
        return view();
    }
}