<?php
namespace app\install\controller;
use framework\base\Controller;
/**
 * 安装模块
 */

class IndexController extends Controller {

	public function __construct(){
        define('__PUBLIC__', substr(PUBLIC_URL, 0, -1));
        define('__ROOT__', substr(ROOT_URL, 0, -1));
        include_once ROOT_PATH . 'app/base/util/Function.php';
        include_once ROOT_PATH . 'app/install/util/Function.php';
		$this->lock = ROOT_PATH . 'install.lock';
		if(is_file($this->lock)){
            $this->redirect(url('home/Index/index'));
        }
	}

	/**
     * 主页
     */
    public function index(){
        $this->display();
    }

    /**
     * 安装环境检测
     */
    public function setup1(){
    	$this->assign('check_env',check_env());
    	$this->assign('check_func',check_func());
    	$this->assign('check_dirfile',check_dirfile());
        $this->display();
    }

    /**
     * 安装程序
     */
    public function setup2(){
    	$this->assign('cookie',$this->getCode(5).'_');
    	$this->assign('safe_key',$this->getCode(20));
        $this->display();
    }

    /**
     * 开始安装
     */
    public function setup3(){
        $this->display();
        //检测信息
        $data = request('post.');
        if(!$data['DB_HOST']){
        	show_msg('请填写数据库地址！',false);
        }
        if(!$data['DB_PORT']){
        	show_msg('请填写数据库端口！',false);
        }
        if(!$data['DB_NAME']){
        	show_msg('请填写数据库名称！',false);
        }
        if(!$data['DB_USER']){
        	show_msg('请填写数据库用户名！',false);
        }
        if(!$data['DB_PREFIX']){
        	show_msg('请填写数据表前缀！',false);
        }
        if(!$data['SAFE_KEY']){
        	show_msg('请填写安全加密码！',false);
        }
        if(!$data['COOKIE_PREFIX']){
        	show_msg('请填写COOKIE前缀！',false);
        }
        //检查数据库
        $link = @mysql_connect($data['DB_HOST'] . ':' . $data['DB_PORT'], $data['DB_USER'], $data['DB_PWD']);
        if(!$link) {
            show_msg('数据库连接失败，请检查连接信息是否正确！',false);
        }
        $mysqlInfo = mysql_get_server_info($link);
        if($mysqlInfo < '5.1.0') {
            show_msg('mysql版本低于5.1，无法继续安装！',false);
        }

        $status = @mysql_select_db($data['DB_NAME'], $link);
        if(!$status) {
            //尝试创建数据库
            $sql = "CREATE DATABASE IF NOT EXISTS `".$data['DB_NAME']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
            if(!mysql_query($sql)){
                show_msg('数据库'. $data['DB_NAME'].'自动创建失败，请手动建立数据库！',false);
            }
            mysql_select_db($data['DB_NAME'], $link);
        }
        show_msg('数据库检查创建完成...');

        //修改数据库文件
        $file = CONFIG_PATH . 'db.php';
        if(save_config($file, $data)){
            show_msg('配置数据库信息完成...');
        }else{
            show_msg('配置数据库信息失败！请手动修改['.$file.']文件！',false);
        }

        //安装数据库
        $file = ROOT_PATH . 'app/install/data/install.sql';
        $sqlData = \framework\ext\Install::mysql($file, 'dux_', $data['DB_PREFIX']);
        mysql_query("SET NAMES utf8");
        foreach ($sqlData as $sql) {
            $rst = mysql_query($sql);
            if($rst === false){
                show_msg(mysql_error(),false);
            }
        }
        //修改安全配置文件
        $file = CONFIG_PATH . 'performance.php';
        if(save_config($file, $data)){
            show_msg('配置站点安全完成...');
        }else{
            show_msg('配置站点安全失败！请手动修改['.$file.']文件！',false);
        }
        //创建文件锁
        file_put_contents($this->lock, NOW_TIME);
        //安装完毕
        show_msg('安装程序执行完毕！后台默认帐号密码均为：admin');
        $homeUrl = url('home/Index/index');
        $adminUrl = __ROOT__.'/admin.php';
        echo "<script type=\"text/javascript\">insok(\"{$homeUrl}\",\"{$adminUrl}\")</script>";
    }


    /**
     * 生成code
     */
    public function getCode ($length = 5){
	    $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	    $result = '';
	    $l = strlen($str)-1;
	    $num=0;

	    for($i = 0;$i < $length;$i ++){
	        $num = rand(0, $l);
	        $a=$str[$num];
	        $result =$result.$a;
	    }
	return $result;
	}
}