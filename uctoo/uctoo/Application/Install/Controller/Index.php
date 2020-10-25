<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\Install\Controller;
use think\Controller;
use think\Session;

class Index extends Controller{
    //安装首页
    public function index(){
       if(is_file( APP_PATH.'extra/user.php')){
            // 已经安装过了 执行更新程序
            //session('update',true);
            $msg = '请删除install.lock文件后再运行安装程序!';
        }else{
            $msg = '已经成功安装，请不要重复安装!';
        }
        if(is_file(APP_PATH.'extra/install.lock')){
            $this->error($msg);
        }
        return $this->fetch();
    }

    //安装完成
    public function complete(){
        $step = Session::get('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }

        // 写入安装锁定文件
        file_put_contents(APP_PATH.'extra/install.lock', 'lock');
        if(!Session::get('update')){
            //创建配置文件
            $this->assign('info',Session::get('config_file'));
        }
        Session::set('step', null);
        Session::set('error', null);
        Session::set('update',null);
        return $this->fetch();
    }
}