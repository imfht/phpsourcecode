<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\install\controller;
use think\Controller;
use think\Db;
class Install extends controller{
	protected function _initialize() {
		parent::_initialize();
		if (is_file(APP_PATH.'database.php')) {
		   return $this->error('已经成功安装，请不要重复安装!','/');
		}
	}
	public function get_db($config){
								//数据库配置
		$db_config = [
		    // 数据库类型
		    'type'        => 'mysql',
		    // 服务器地址
		    'hostname'    => $config['DB_HOST'],
		 
		    // 数据库名
		    'database'    => $config['DB_NAME'],
		    // 数据库用户名
		    'username'    => $config['DB_USER'],
		    
		    'hostport'    => $config['DB_PORT'],
		    // 数据库密码
		    'password'    => $config['DB_PWD'],
		    // 数据库编码默认采用utf8
		    'charset'     => 'utf8',
		    // 数据库表前缀
		    'prefix'      => $config['DB_PREFIX'],
		];
				
		return Db::connect($db_config);
	}
	
	//安装第一步，检测运行所需的环境设置
    public function step1(){
        session('error', false);

        //环境检测
        $env = check_env();

        //目录文件读写检测       
        $dirfile = check_dirfile();
        $this->assign('dirfile', $dirfile);
      

        //函数检测
        $func = check_func();

        session('step', 1);

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
                session('admin_info', $info);
            }

            //检测数据库配置
            if(!is_array($db) || empty($db[0]) ||  empty($db[1]) || empty($db[2]) || empty($db[3])){
                $this->error('请填写完整的数据库配置');
            } else {
                $DB = array();
                list($DB['DB_TYPE'], $DB['DB_HOST'], $DB['DB_NAME'], $DB['DB_USER'], $DB['DB_PWD'],
                    $DB['DB_PORT'], $DB['DB_PREFIX']) = $db;
                //缓存数据库配置
                cookie('db_config',$DB);

                //创建数据库
               $dbname = $DB['DB_NAME'];
               // unset($DB['DB_NAME']);
				
                $db  =$this->get_db($DB);
				/*
                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";

                try{
                   if(!$db->execute($sql)){
                   		$this->error($db->getError());exit;
                   }
                }catch (\think\Exception $e){
                    if(strpos($e->getMessage(),'getaddrinfo failed')!==false){
                        $this->error( '数据库服务器（数据库服务器IP） 填写错误。');// 提示信息
                    }
                   if(strpos($e->getMessage(),'Access denied for user')!==false){
                       $this->error('数据库用户名或密码 填写错误。');// 提示信息
                   }else{
                       $this->error( $e->getMessage());// 提示信息
                   }
                }
				*/
                session('step',2);
               
            }

            //跳转到数据库安装页面
            $this->redirect('step3');
        } 

        session('error') && $this->error('环境检测没有通过，请调整环境后重试！');

        $step = session('step');
        if($step != 1 && $step != 2){
           $this->redirect('step1');
        }

        session('step', 2);
        return $this->fetch();

        
    }

	public function step3(){
        if(session('step') != 2){
            $this->redirect('step2');
        }
		session('step', 3);
		
		return $this->fetch();
    }
	
	function insert_db(){
		 //连接数据库
            $dbconfig = cookie('db_config');
            $db  =$this->get_db($dbconfig);			
            //创建数据表
			
            createTables($db, $dbconfig['DB_PREFIX']);
            //注册创始人帐号
            $auth  = build_auth_key();
            $admin = session('admin_info');

            register_administrator($db, $dbconfig['DB_PREFIX'], $admin, $auth);

            //创建配置文件
            write_config($dbconfig, $auth);            
			
			return ['success'=>'安装成功','url'=>url('Index/complete')];
			
	}
	
}
