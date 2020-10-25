<?php
namespace app\install\controller;

use think\Controller;
use think\Db;

class Install extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        if (is_file(ROOT_PATH . 'install.lock'))
        {
            // 已经安装过了 执行更新程序
            $msg = '请删除install.lock文件后再运行安装程序!';
            $this->error($msg);
        }
    }

    //安装第一步，检测运行所需的环境设置
    public function step1(){
        //初始session
        session('db_config',null);
        session('admin_info',null);
        session('config_file',null);
        session('error', false);
        //环境检测
        $muu_env = check_env();
        //函数依赖检测
        $func = check_func();
        //数据库配置文件
		$dbConfigFile = APP_PATH . 'database.php';
        //目录文件读写检测
        if(is_really_writable($dbConfigFile)){
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }
        session('step', 1);
        if(isset($muu_env)){
        	$this->assign('muu_env', $muu_env);
        }
        $this->assign('func', $func);
        return $this->fetch();
    }

    //安装第二步，创建数据库
    public function step2($db = null, $admin = null){
        
        if($this->request->isPost()){

            //检测管理员信息
            if(!is_array($admin) || empty($admin[0]) || empty($admin[1]) || empty($admin[3])){
                $this->error('请填写完整管理员信息');
            } else if($admin[1] != $admin[2]){
                $this->error('确认密码和密码不一致');
            } else {
                $info = array();
                list($info['username'], $info['password'], $info['repassword'], $info['email']) = $admin;
                //缓存管理员信息
                session('admin_info', $info);
            }

            //检测数据库配置
            if(!is_array($db) || empty($db[0]) ||  empty($db[1]) || empty($db[2]) || empty($db[3])){
                $this->error('请填写完整的数据库配置');
            } else {

                $dbname = $db[2];
				//数据库配置
	            $dbconfig['type']     = $db[0];
	            $dbconfig['hostname'] = $db[1];
	            $dbconfig['username'] = $db[3];
	            $dbconfig['password'] = $db[4];
	            $dbconfig['hostport'] = $db[5];
                // 创建数据库连接
            	$db_instance = Db::connect($dbconfig);
                // 检测数据库连接
	            try {
	                $db_instance->execute('select version()');
	            } catch (\Exception $e) {
	                $this->error('数据库连接失败，请检查数据库配置！', 'install/Index/step2');
	            }

	            //建立数据库
            	$sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            	$db_instance->execute($sql) || $this->error($db_instance->getError(), 'install/Index/step2');
                
            	//完整数据库配置
	            $dbconfig['database'] = $dbname;
	            $dbconfig['prefix']   = $db[6];
	            //暂存数据库配置
	            session('db_config',$dbconfig);
                session('step',2);
            }

            //跳转到数据库安装页面
            $this->redirect('step3');
        } else {
                session('error') && $this->error('环境检测没有通过，请调整环境后重试！');
                session('step', 2);
                return $this->fetch();

        }
    }

    //安装第三步，安装数据表，创建配置文件
    public function step3(){
        if(session('step') != 2){
            $this->redirect('step2');
        }

        echo $this->fetch();

        //连接数据库
        $dbconfig = session('db_config');
        $db_instance = Db::connect($dbconfig);
        //创建数据表

        create_tables($db_instance, $dbconfig['prefix']);
        //注册创始人帐号
        $auth  = build_auth_key();
        $admin = session('admin_info');
        register_administrator($db_instance, $dbconfig['prefix'], $admin, $auth);

        //更新配置文件
        $conf   =   write_config($dbconfig, $auth);
        session('config_file',$conf);

        if(session('error')){
            error_btn('很遗憾，安装失败，请检测后重新安装！','btn btn-warning btn-large btn-block');
        } else {
            session('step', 3);
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='".Url('Index/complete')."'},5000)</script>";
        }
    }

    public function tip($info,$title='很遗憾，安装失败，失败原因'){
        $this->assign('info',$info);// 提示信息
        $this->assign('title',$title);
        return view('error');exit;
    }
}