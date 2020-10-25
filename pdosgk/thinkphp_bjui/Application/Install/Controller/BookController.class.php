<?php

/**
 * 小说安装模块
 * @author Lain
 *
 */
namespace Install\Controller;
use Think\Controller;
class BookController extends Controller {

	private $errormsg;

	/**
	 * [_initialize 初始化]
	 * @return [type] [description]
	 */
	public function _initialize()
	{
         if(is_file('./Application/Weixin/Conf/config.php'))
            E('请先删除Intall目录下面install.lock文件');
        
	}

	/**
	 * [index 欢迎方法]
	 * @return [type] [description]
	 */
    public function index()
    {
        session('step',false);
		$this->display();
    }


    /**
     * [step1 检查环境和目录权限]
     * @return [type] [description]
     */
    public function step1()
    {

        session('step',false);
    	// 需要可以读写的目录
    	$dir = array(
    		//array('name'=>'./ThinkPHP','text'=>'主程序','status'=>0),
    		array('name'=>'./Application','text'=>'配置文件、数据、缓存','status'=>0),
    	);

    	foreach ($dir as $k=> $v) 
    	{
    		if(is_writable($v['name'])) $dir[$k]['status']=1;
    	}
    	$this->assign('dir',$dir);


    	// 环境
    	$method = array(
    		array('name'=>'mbsring扩展','function'=>'mb_substr','text'=>'字符串处理','status'=>0),
    		array('name'=>'gd库','function'=>'imagecopy','text'=>'图像处理','status'=>0),
    	);
    	foreach ($method as $k=> $v) 
    	{
    		if(function_exists($v['function'])) $method[$k]['status']=1;
    	}
		$this->assign('method',$method);

    	$this->display();
    }




    /**
     * [step2 第二步 设置数据库链接和后台管理员账号]
     * @return [type] [description]
     */
    public function step2()
    {
    	if(IS_POST)
    	{
    		// 执行验证
    		//if(!$this->_verify()) $this->error($this->errormsg);
    		// 执行写入配置文件
    		$this->_write_config();
    		// 执行sql文件
    		$this->_sql_query();
            session('step',true);
    		// 跳转
    		$this->redirect('Book/step3');
    	}
        session('step',false);
    	$this->display();
    }


    /**
     * [step3 第三步设置lock文件，提示系统安装成功]
     * @return [type] [description]
     */
    public function step3()
    {   
        if(!session('step'))
            $this->redirect('Book/index');
        // 创建文件
        //file_put_contents(APP_PATH.'/Install/install.lock', '');
    	$this->display();
    }


	//没有用到
    public function ajaxcheck()
    {
    	echo 1;
    	die;
    }

   
    /*********************************验证******************************************/


    private function  _verify()
    {
        // 验证必填
        $TOKEN = I('post.token');
        $APPID = I('post.appid');
        $APPSECRET = I('post.appsecret');
        $TULING_KEY = I('post.tuling_key');



        // 微信信息验证
        if(!$TOKEN)
        {
            $this->errormsg='请输入Token';
            return false;
        }
        if(!$APPID)
        {
            $this->errormsg='请输入Appid';
            return false;
        }
        if(!$APPSECRET)
        {
            $this->errormsg='请输入Appsecret';
            return false;
        }

        // 图灵key验证
        if(!$TULING_KEY)
        {
            $this->errormsg='请输入图灵key';
            return false;
        }

       return ture;
    }

    /**
     * [_write_config 写配置文件]
     * @return [type] [description]
     */
    private function _write_config()
    {

    	
        $TOKEN = I('post.token');
        $APPID = I('post.appid');
        $APPSECRET = I('post.appsecret');
        $TULING_KEY = I('post.tuling_key');

        $data['BOOK_DEBUG']		=	true;
        $data['TOKEN'] 			= $TOKEN;
        $data['APPID'] 			= $APPID;
        $data['APPSECRET'] 		= $APPSECRET;
        $data['TULING_KEY ']	= $TULING_KEY;

        $config = var_export($data,true);

        $php =<<<str
<?php
/**[数据配置文件]
 * @Author: 165490077@qq.com
 * @Date:   2015-08-29 11:07:35
 */
 
return $config;
str;
        //写入文件
        $result = file_put_contents('./Application/Weixin/Conf/config.php', $php);
        
        return true;
    }


    /**
     * [_sql_query 执行sql文件]
     * @return [type] [description]
     */
    private function _sql_query()
    {
    	
        if(!mysql_connect(C('DB_HOST'), C('DB_USER'), C('DB_PWD')))
            return false;
        
        $dbname = C('DB_NAME');
        
        // 选择数据库
        mysql_select_db($dbname);
        // 设置编码
        mysql_query("set names utf8");

        // 表前缀
        $dbPrefix = C('DB_PREFIX');
        $sql = file_get_contents(APP_PATH.MODULE_NAME.'/Conf/db_book.sql');
        
        $sql=preg_split('/;{}/', $sql);
     /*   header("Content-type:text/html;charset=utf-8");
        echo '<pre>';
        print_r($sql);
        die;*/


        foreach ($sql as $k => $v) 
        {
            // 过滤注释
            $preg="/(\/\*.*\*\/)/isU";
            preg_match_all($preg, trim($v),  $temp);
            $v=str_replace($temp[1], '', $v);
            // 过滤注释
            $temp=array();
            $preg="/(--.*\r\n)/isU";
            preg_match_all($preg, trim($v),  $temp);
            $v=str_replace($temp[1], '', $v);
            // 替换表前缀
            $v=str_replace('db_', $dbPrefix, $v);
            
            // 执行mysql
            if($v)
            {
                if(!mysql_query($v)) 
                {
                    header("Content-type:text/html;charset=utf-8");
                    echo $v."<br />";
                    die(mysql_error());
                }
            }
            
        } 

        // die;
        return true;
    }



    /**
     * [_add_user 添加用户]
     */
    private function _add_user()
    {
        // 管理员账号
        $username = I('post.username');
        // 管理员昵称
        $nickname = I('post.nickname');
        // 登录密码
        $password = hash_hmac('sha256',I('post.password'), $username);
        // 邮箱
        $email = I('post.email');
        // 表前缀
        $dbPrefix = I('post.dbprefix');
        // IP
        //$ip = get_client_ip(0, true);
        // 当前时间
        //$time = time();
 

        // 组合sql
        $sql="INSERT INTO `{$dbPrefix}admin`(userid,username,password,roleid,realname,email) VALUES (1, '{$username}', '{$password}', '1', '{$nickname}', '{$email}')";

        // 执行插入
        mysql_query($sql);

        return true;
    }

	}