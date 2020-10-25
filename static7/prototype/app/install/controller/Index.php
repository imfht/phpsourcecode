<?php

namespace app\install\controller;

use think\Session;
use think\View;
use think\Config;
use Storage\Storage;

class Index {

    //引入jump类
    use \traits\controller\Jump;

    protected $view;
    protected $storage;

    public function __construct() {
        $this->storage = Storage::instance();
        $this->view = View::instance([], Config::get('replace_str'));
    }

    /**
     * 安装首页
     * @author static7 <static7@qq.com>
     * @return type
     */
    public function index() {
        if (is_file(APP_PATH . 'database.php')) {
            Session::set('update',1, 'install'); // 已经安装过了 执行更新程序
            $msg = '请删除data/install.lock文件后再运行升级!';
        } else {
            $msg = '已经成功安装了本系统，请不要重复安装!';
        }
        if ($this->storage->has(APP_PATH . '../data/install.lock')) {
            return $this->error($msg);
        }
        return $this->view->fetch();
    }

    /**
     * 安装完成
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function complete() {
        $step = Session::get('step', 'install');
        if (!$step) {
            $this->redirect('index');
        } elseif ($step != 3) {
            return $this->redirect("Install/step{$step}");
        }
        // 写入安装锁定文件
        $this->storage->put(APP_PATH . '../data/install.lock', 'lock');
        if (!Session::get('update','install')) {
            //创建配置文件
            $this->view->assign('info', Session::get('config_file','install'));
        }
        Session::delete('step', 'install');
        Session::delete('error', 'install');
        Session::delete('update', 'install');
        return $this->view->fetch();
    }

}
