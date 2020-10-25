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
use think\Db;
use think\Session;

class Install extends Controller{

    protected function _initialize(){
        if(is_file(APP_PATH. 'extra/install.lock')){
            $this->error('已经成功安装，请不要重复安装!');
        }
    }

    //安装第一步，检测运行所需的环境设置
    public function step1(){
        Session::set('error', false);

        //环境检测
        $env = check_env();

        //目录文件读写检测
        if(IS_WRITE){
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }

        //函数检测
        $func = check_func();

        Session::set('step', 1);

        $this->assign('env', $env);
        $this->assign('func', $func);
        return $this->fetch();
    }

    //安装第二步，创建数据库
    public function step2($db = null, $admin = null){
        if(request()->isPost()){

            //检测管理员信息
            if(!is_array($admin) || empty($admin[0]) || empty($admin[1]) || empty($admin[3])){
                $this->error('请填写完整管理员信息');
            } else if($admin[1] != $admin[2]){
                $this->error('确认密码和密码不一致');
            } else {
                $info = array();
                list($info['username'], $info['password'], $info['repassword'], $info['email'])
                    = $admin;
                //缓存管理员信息
                Session::set('admin_info', $info);
            }

            //检测数据库配置
            if(!is_array($db) || empty($db[0]) ||  empty($db[1]) || empty($db[2]) || empty($db[3])){
                $this->error('请填写完整的数据库配置');
            } else {
                $DB = array();
                list($DB['type'], $DB['hostname'], $DB['database'], $DB['username'], $DB['password'],
                    $DB['hostport'], $DB['prefix']) = $db;
                //缓存数据库配置
                cookie('db_config',$DB);

                //创建数据库
                $dbname = $DB['database'];
                unset($DB['database']);

                $db  = Db::connect($DB);

                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";

                try{
                    $db->execute($sql);
                }catch (\think\Exception $e){
                    if(strpos($e->getMessage(),'getaddrinfo failed')!==false){
                        $this->error( '数据库服务器（数据库服务器IP） 填写错误。','很遗憾，创建数据库失败，失败原因');// 提示信息
                    }
                   if(strpos($e->getMessage(),'Access denied for user')!==false){
                       $this->error('数据库用户名或密码 填写错误。','很遗憾，创建数据库失败，失败原因');// 提示信息
                   }else{
                       $this->error( $e->getMessage());// 提示信息
                   }
                }
                Session::set('step',2);
                // $this->error($db->getError());exit;
            }

            //跳转到数据库安装页面
            $this->redirect('step3');
        } else {
            Session::get('error') && $this->error('环境检测没有通过，请调整环境后重试！');

                $step = Session::get('step');
                if($step != 1 && $step != 2){
                   // $this->redirect('step1');
                }

            Session::set('step', 2);
                return $this->fetch();

        }
    }

    //安装第三步，安装数据表，创建配置文件
    public function step3(){
       /* if(session('step') != 2){
            $this->redirect('step2');
        }*/

        echo $this->fetch();


            //连接数据库
            $dbconfig = cookie('db_config');
            $db = Db::connect($dbconfig);
            //创建数据表

            create_tables($db, $dbconfig['prefix']);
            //注册创始人帐号
            $auth  = build_auth_key();
            $admin = Session::get('admin_info');
            register_administrator($db, $dbconfig['prefix'], $admin, $auth);

            //创建配置文件
            $conf   =   write_config($dbconfig, $auth);
            Session::set('config_file',$conf);


        if(Session::get('error')){
            //show_msg();
        } else {
            Session::set('step', 3);

            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='".url('Index/complete')."'},5000)</script>";
            //ob_flush();
            Session::flush();
           //$this->redirect('Index/complete');
        }
    }

}