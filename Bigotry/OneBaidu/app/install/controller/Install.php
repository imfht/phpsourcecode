<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\install\controller;

use \think\Db;

/**
 * Install
 */
class Install extends InstallBase
{

    //安装第一步，检测运行所需的环境设置
    public function step1()
    {
        session('error', false);

        //环境检测
        $env = check_env();
        
        // 自动识别SAE环境  目录文件读写检测
        if (!function_exists('saeAutoLoader')) {
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }

        //函数检测
        $func = check_func();

        session('step', 1);

        $this->assign('env', $env);
        
        $this->assign('func', $func);
        
        return view('install/step1');
    }
    
    
    
    
    //安装第二步，创建数据库
    public function step2($db = null, $admin = null)
    {
        
        if ($this->request->isPost()) {
            
            //检测管理员信息
            if (!is_array($admin) || empty($admin[0]) || empty($admin[1]) || empty($admin[3])) {
                
                $this->error('请填写完整管理员信息');
                
            } else if ($admin[1] != $admin[2]) {
                
                $this->error('确认密码和密码不一致');
                
            } else {
                
                $info = array();
                list($info['username'], $info['password'], $info['repassword'], $info['email']) = $admin;
                //缓存管理员信息
                session('admin_info', $info);
            }

            //检测数据库配置
            if (!is_array($db) || empty($db[0]) ||  empty($db[1]) || empty($db[2]) || empty($db[3])) {
                
                $this->error('请填写完整的数据库配置');
                
            } else {
                
                $db_config = array();
                
                list($db_config['DB_TYPE'], $db_config['DB_HOST'], $db_config['DB_NAME'], $db_config['DB_USER'], $db_config['DB_PWD'],
                     $db_config['DB_PORT'], $db_config['DB_PREFIX']) = $db;
                
                //缓存数据库配置
                session('db_config', $db_config);
                
                //创建数据库
                $dbname = $db_config['DB_NAME'];
                
                $db_object = Db::connect($db_config['DB_TYPE'].'://'.$db_config['DB_USER'].':'.$db_config['DB_PWD'].'@'.$db_config['DB_HOST'].':'.$db_config['DB_PORT']);
                
                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
                
                $db_object->execute($sql) || $this->error($db_object->getError());
            }

            //跳转到数据库安装页面
            $this->redirect('step3');
            
        } else {
            
            session('error') && $this->error('环境检测没有通过，请调整环境后重试！');

            $step = session('step');
            
            if($step != 1 && $step != 2) {

                $this->redirect('step1'); 
            }

            session('step', 2);

            return view('step2');
        }
    }
    
    
    
    //安装第三步，安装数据表，创建配置文件
    public function step3()
    {
        
        if (session('step') != 2) {
            
            $this->redirect('step2'); 
        }
 
        //连接数据库
        $db_config = session('db_config');
        
        $db_object = Db::connect($db_config['DB_TYPE'].'://'.$db_config['DB_USER'].':'.$db_config['DB_PWD'].'@'.$db_config['DB_HOST'].':'.$db_config['DB_PORT'].'/'.$db_config['DB_NAME'].'#utf8');
        
        //创建数据表
        create_tables($db_object, $db_config['DB_PREFIX']);
        
        //注册创始人帐号
        $auth  = build_auth_key();
        $admin = session('admin_info');
        
        register_administrator($db_object, $db_config['DB_PREFIX'], $admin, $auth);

        //创建配置文件
        $conf = write_config($db_config, $auth);
        
        session('config_file',$conf);

        if (session('error')) {
            
            die('install error!');
        } else {
            
            session('step', 3);
            
            $this->redirect('index/index/index');
        }
    }
    
}
