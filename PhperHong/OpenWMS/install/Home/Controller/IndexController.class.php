<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

    	//检查环境
    	$phpv = phpversion();
	    $sp_os = PHP_OS;
	 
	    $sp_server = $_SERVER['SERVER_SOFTWARE'];
	    $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
	    $sp_name = $_SERVER['SERVER_NAME'];
	    $sp_max_execution_time = ini_get('max_execution_time');
	    $sp_allow_reference1 = false;
	    $sp_allow_reference = '<font color=red>[×]Off</font>';
	    if (ini_get('allow_call_time_pass_reference')){
	    	$sp_allow_reference1 = true;
	    	$sp_allow_reference = '<font color=green>[√]On</font>';
	    }
	    $sp_allow_url_fopen = '<font color=red>[×]Off</font>';
	    $sp_allow_url_fopen1 = false;
	    if (ini_get('allow_url_fopen')){
	    	$sp_allow_url_fopen = '<font color=green>[√]On</font>';
	    	$sp_allow_url_fopen1 = true;
	    }
	    $sp_safe_mode = '<font color=red>[×]Off</font>';
	    $sp_safe_mode1 = false;
	    if (!ini_get('safe_mode')){
	    	$sp_safe_mode = '<font color=green>[√]On</font>';
	    	$sp_safe_mode1 = true;
	    }

	    $sp_gd = '<font color=red>[×]Off</font>';
	    $sp_gd1 = false;
	    if ($this->gdversion()){
	    	$sp_gd = '<font color=green>[√]On</font>';
	    	$sp_gd1 = true;
	    }
	    $sp_mysql = '<font color=red>[×]Off</font>';
	    $sp_mysql1 = false;
	    if (function_exists('mysql_connect')){
	    	$sp_mysql = '<font color=green>[√]On</font>';
	    	$sp_mysql1 = true;
	    }
	    $sp_mb_string = '<font color=red>[×]Off</font>';
	    $sp_mb_string1 = false;
	    if (function_exists('mb_strlen')){
	    	$sp_mb_string = '<font color=green>[√]On</font>';
	    	$sp_mb_string1 = true;
	    }


	   
	    
	   
	   
	   
	  	$sp_redis =  '<font color=green>[√]On</font>';
	  	$sp_redis1 = true;
	  	try {
	  		\Predis\Autoloader::register();  
        	$handler  = new \Predis\Client('tcp://127.0.0.1:6379');
        	$value = $handler->get('user');

	  	} catch (\Predis\PredisException $e) {
	  		$sp_redis = '<font color=red>[×]Off</font>';
	  		$sp_redis1 = false;
	  	}
	     
	    if($sp_mysql=='<font color=red>[×]Off</font>')
	    $sp_mysql_err = TRUE;
	    else
	    $sp_mysql_err = FALSE;

	    $sp_testdirs = array(
	        '/',
	        '/admin/upload/*',
	        '/src/Runtime/*',
	        '/src/Common/Conf/*',
	      
	    );
   		$this->assign(array(
            'phpv'             => $phpv,
            'sp_os'          => $sp_os,
            'sp_gd'             => $sp_gd,
            'sp_server'             => $sp_server,
            'sp_host'           => $sp_host,
            'sp_name'        => $sp_name,
            'sp_max_execution_time'=>$sp_max_execution_time,
            'sp_allow_reference'	=> $sp_allow_reference,
            'sp_allow_url_fopen'	=> $sp_allow_url_fopen,
            'sp_safe_mode'			=> $sp_safe_mode,
            'sp_gd'					=> $sp_gd,
            'sp_mysql'				=> $sp_mysql,
            'sp_mb_string'			=> $sp_mb_string,
         	'sp_testdirs'			=> $sp_testdirs,
         	'sp_redis'				=> $sp_redis,
         	'sp_max_execution_time1'=>$sp_max_execution_time1,
            'sp_allow_reference1'	=> $sp_allow_reference1,
            'sp_allow_url_fopen1'	=> $sp_allow_url_fopen1,
            'sp_safe_mode1'			=> $sp_safe_mode1,
            'sp_gd1'					=> $sp_gd1,
            'sp_mysql1'				=> $sp_mysql1,
            'sp_mb_string1'			=> $sp_mb_string1,
         	'sp_testdirs1'			=> $sp_testdirs1,
         	'sp_redis1'				=> $sp_redis1,
        ));
    	$this->display();
    }
    /**
	* 安装
    */
    public function install(){

    	$param = array(
    		'host'	=> I('post.host'),
    		'username'	=> I('post.username'),
    		'password'	=> I('post.password'),
    		'dbname'	=> I('post.dbname'),
    		'uname'	=> I('post.uname'),
    		'pw'	=> I('post.pw'),
    	);

    	$conn = mysql_connect($param['host'], $param['username'], $param['password']) or die(json_encode(array('msg'=>'数据库服务器或登录密码无效，无法连接数据库，请重新设定！', 'ret'=>0)));

	    mysql_query("CREATE DATABASE IF NOT EXISTS `".$param['dbname']."`;",$conn);
	    
	    mysql_select_db($param['dbname']) or die(json_encode(array('msg'=>'选择数据库失败，可能是你没权限，请预先创建一个数据库！', 'ret'=>0)));

	    //获得数据库版本信息
	    $rs = mysql_query("SELECT VERSION();",$conn);
	    $row = mysql_fetch_array($rs);
	    $mysqlVersions = explode('.',trim($row[0]));
	    $mysqlVersion = $mysqlVersions[0].".".$mysqlVersions[1];

	    mysql_query("SET NAMES utf8,character_set_client=binary,sql_mode='';",$conn);
	    
	    //导入数据库

	  	$query = '';
	    $fp = fopen(OPENWMSROOT."/install/Public/sql/openWMS.sql",'r');
	    while(!feof($fp))
	    {
	        $line = rtrim(fgets($fp,1024));
	        if(preg_match("#;$#", $line))
	        {
	            $query .= $line."\n";
	           
	            if($mysqlVersion < 4.1)
	            {
	                $rs = mysql_query($query,$conn);
	            } else {
	                if(preg_match('#CREATE#i', $query))
	                {
	                    $rs = mysql_query(preg_replace("#TYPE=MyISAM#i",$sql4tmp,$query),$conn);
	                }
	                else
	                {
	                    $rs = mysql_query($query,$conn);
	                }
	            }
	            $query='';
	        } else if(!preg_match("#^(\/\/|--)#", $line))
	        {
	            $query .= $line;
	        }
	    }
	    fclose($fp);

	    //添加登陆账号
	    $password = substr($param['pw'], 5, 5);
 	
 		$adminsql = "insert  into `jk_admins`(`id`,`username`,`password`,`activated`,`last_login`,`parent_uid`,`group_id`,`created_at`,`updated_at`)value (1,'".$param['uname']."','".md5($password)."',1,'0000-00-00 00:00:00',0,1,'0000-00-00 00:00:00','0000-00-00 00:00:00');";
 		$rs = mysql_query($adminsql,$conn);
 		//修改配置文件
 		$this->update_config(array(
 			'DB_HOST'               => $param['host'], 
			'DB_NAME'               => $param['dbname'],   	// 数据库名 
			'DB_USER'               => $param['username'],      // 用户名
			'DB_PWD'                => $param['password'],    		// 密码
		), 'db.php');

    	echo json_encode(array('msg'=>'安装完成！正跳转到管理平台登陆页面。', 'ret'=>1));
    }
    //修改配置文件
	protected function update_config($new_config, $filename) {
		$config_file = OPENWMSROOT . '/src/Common/Conf/'.$filename;
		if (is_writable($config_file)) {
			$config = require $config_file;
			$config = array_merge($config, $new_config);
			file_put_contents($config_file, "<?php \nreturn " . var_export($config, true) . ";", LOCK_EX);
		
			return true;
		} else {
			return false;
		}
	}



    function gdversion(){
	  //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
	  if(!function_exists('phpinfo'))
	  {
	      if(function_exists('imagecreate')) return '2.0';
	      else return 0;
	  }
	  else
	  {
	    ob_start();
	    phpinfo(8);
	    $module_info = ob_get_contents();
	    ob_end_clean();
	    if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches)) {   $gdversion_h = $matches[1];  }
	    else {  $gdversion_h = 0; }
	    return $gdversion_h;
	  }
	}
}


