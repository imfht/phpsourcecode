<?php

/**
 * 主框架安装模块
 * @author Lain
 *
 */
namespace Install\Controller;
use Think\Controller;
class IndexController extends Controller {

    private $errormsg;
    public $con;

    /**
     * [_initialize 初始化]
     * @return [type] [description]
     */
    public function _initialize()
    {
        if(is_file(APP_PATH.'/Install/install.lock'))
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
            array('name'=>'./Application/Runtime','text'=>'配置文件、数据、缓存','status'=>0),
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
            array('name'=>'mysqli库','function'=>'mysqli_info','text'=>'数据库','status'=>0),
            array('name'=>'pdo_mysqli库','function'=>'pdo_drivers','text'=>'pdo数据库','status'=>0),
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
            $this->display('create_table');
            // 执行验证
            if(!$this->_verify()) $this->error($this->errormsg);
            //连接数据库
            $data = I('');
            $this->con = mysqli_connect($data['dblink'],$data['dbuser'],$data['dbpassword'],$data['dbname'], $data['dbport']);
            if(!$this->con){
                $this->error('dbbase connect error!');
            }
            // 执行写入配置文件
            $this->_write_config();
            // 执行sql文件
            $this->_sql_query();
            // 创建用户
            $this->_add_user();
            session('step',true);
            // 跳转
            echo "<script type=\"text/javascript\">setTimeout(function(){location.href='".U('Index/step3')."'},5000)</script>";
            ob_flush();
            flush();
            //$this->redirect('Index/step3');
        }else{
            session('step',false);
            $this->display();
        }
    }


    /**
     * [step3 第三步设置lock文件，提示系统安装成功]
     * @return [type] [description]
     */
    public function step3()
    {   
        if(!session('step'))
            $this->redirect('Index/index');
        // 创建文件
        file_put_contents(APP_PATH.'/Install/install.lock', '');
        $this->display();
    }



    /**
     * [ajaxcheck ajax验证数据库链接是否正常]
     * @return [type] [description]
     */
    public function ajaxcheck()
    {
        $data['dblink'] = I('post.dblink');
        $data['dbuser'] = I('post.dbuser');
        $data['dbpassword'] = I('post.dbpassword');
        $data['dbport'] = I('post.dbport');
        //dump($data);die;

        if($this->_checkdb($data))
            echo 1;
        else
            echo 0;
        die;
    }

   
    /*********************************验证******************************************/


    private function  _verify()
    {
        // 验证必填
        $dblink = I('post.dblink');
        $dbname = I('post.dbname');
        $dbprefix =I('post.dbprefix');
        $dbuser = I('post.dbuser');
        $dbpassword = I('post.dbpassword');
        $dbport = I('post.dbport');

        $username = I('post.username');
        $nickname = I('post.nickname');
        $password = I('post.password');
        $email = I('post.email');


        // 数据库链接信息验证
        if(!$dblink)
        {
            $this->errormsg='请输入数据库链接地址';
            return false;
        }
        if(!$dbname)
        {
            $this->errormsg='请输入数据库名称';
            return false;
        }
        if(!$dbprefix)
        {
            $this->errormsg='请输入数据库表前缀';
            return false;
        }
        if(!$dbuser)
        {
            $this->errormsg='请输入数据库用户名';
            return false;
        }
        if(!$dbport)
        {
            $this->errormsg='请输入数据库端口';
            return false;
        }
        if(!$this->_dbpassword())
        {
            $this->errormsg='数据库密码不正确';
            return false;
        }

        // 管理员账号验证
        if(!$username)
        {
            $this->errormsg='请输入管理员账号';
            return false;
        }

        if(!$username)
        {
            $this->errormsg='请输入管理员账号';
            return false;
        }


        if(!$nickname)
        {
            $this->errormsg='请输入管理员昵称';
            return false;
        }
        if(!$email)
        {
            $this->errormsg='请输入管理员邮箱';
            return false;
        }

        if(!preg_match('/^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/i', $email))
        {
            $this->errormsg='邮箱格式不正确';
            return false;
        }
        

       
   
       return ture;
    }


    /**
     * [_dbpassword 检查数据库连接]
     * @return [type] [description]
     */
    private function _dbpassword()
    {
        $data['dblink'] = I('post.dblink');
        $data['dbuser'] = I('post.dbuser');
        $data['dbpassword'] = I('post.dbpassword');
        $data['dbport'] = I('post.dbport');

        // 执行验证
        if($this->_checkdb($data))
            return true;
        else
            return false;
    }



     /**
     * [_checkdb 验证数据链接]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function _checkdb($data)
    {
        if(mysqli_connect($data['dblink'],$data['dbuser'],$data['dbpassword'],$data['dbname'], $data['dbport']))
            return true;
        else
            return false;
    }

    /**
     * [_write_config 写配置文件]
     * @return [type] [description]
     */
    private function _write_config()
    {

        $dbuser = I('post.dbuser');
        $dbpassword = I('post.dbpassword');
        $dblink = I('post.dblink');
        $dbname = I('post.dbname');
        $dbport = I('post.dbport');

        $data['DB_TYPE'] = 'mysqli';
        $data['DB_HOST'] = $dblink;
        $data['DB_NAME'] = $dbname;
        $data['DB_USER'] = $dbuser;
        $data['DB_PWD']  = $dbpassword;
        $data['DB_PORT'] = $dbport;
        $data['DB_PREFIX'] = I('post.dbprefix');
        $data['DB_CHARSET'] = 'utf8';

        $config = var_export($data,true);

        $php =<<<str
<?php
/**[数据配置文件]
 * @Author: 165490077@qq.com
 * @Date:   2014-08-15 11:07:35
 * @Last Modified by:   Lain
 * @Last Modified time: 2015-05-04 09:17:38
 */
// 数据库连接信息=>数据库类型://用户名:密码@链接地址:密码/数据库名称
// auth权限设置
// AUTH_ON           认证开关
// AUTH_TYPE         认证方式，1为时时认证；2为登录认证。
// AUTH_GROUP        用户组数据表名
// AUTH_GROUP_ACCESS 用户组明细表
// AUTH_RULE         权限规则表
// AUTH_USER         用户信息表
return $config;
str;
        //写入文件
        $result = file_put_contents('./Application/Common/Conf/config.php', $php);
        
        return true;
    }


    /**
     * [_sql_query 执行sql文件]
     * @return [type] [description]
     */
    private function _sql_query()
    {
        $installdata = I('post.installdata');
        $dbname = I('post.dbname');
        // 创建数据库
        mysqli_query($this->con, "CREATE DATABASE IF NOT EXISTS `".$dbname."` default   charset   utf8;");
        // 选择数据库
        // mysql_select_db($dbname);
        // 设置编码
        mysqli_query($this->con, "set names utf8");

        // 表前缀
        $dbPrefix=I('post.dbprefix');
        $sql = $installdata?file_get_contents(APP_PATH.'../db_thinkphp+bjui.sql'):file_get_contents(APP_PATH.MODULE_NAME.'/Conf/db_thinkphp+bjui.sql');
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", $sql);
        //$orginal = C('ORIGINAL_TABLE_PREFIX');
        $sql = str_replace(" `db_", " `{$dbPrefix}", $sql);
        
        
        //开始安装
        show_msg('开始安装数据库...');
        foreach ($sql as $value) {
            $value = trim($value);
            if (empty($value)) continue;
            if (substr($value, 0, 12) == 'CREATE TABLE') {
                $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                $msg = "创建数据表{$name}";
                if (false !== mysqli_query($this->con, $value)) {
                    show_msg($msg . '...成功');
                } else {
                    show_msg($msg . '...失败');
                }
            } else {
                mysqli_query($this->con, $value);
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
        // 组合sql
        $sql="INSERT INTO `{$dbPrefix}admin`(userid,username,password,roleid,nickname,email,`status`) VALUES (1, '{$username}', '{$password}', '1', '{$nickname}', '{$email}',  1)";
        
        // 执行插入
        mysqli_query($this->con, $sql);

        return true;
    }

    }