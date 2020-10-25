<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/17 0017
 * Time: 15:25
 * 所有公共方法都有onStart  onEnd方法
 */
class basePlugin extends Plugin{

   public  function  init_timezone(){
       @date_default_timezone_set('Asia/Shanghai');
   }

   public  function   init_session(){
       $domain= explode(':',$_SERVER['HTTP_HOST']);
        $config=c();
       // $subdomain_suffix;die; 子域名后缀124.21.206:8090
       //session.name强制定制成PHPSESSID,不请允许更改
       @ini_set('session.name','PHPSESSID');
       // $subdomain_suffix = str_replace('http://','',$domain[0]);

       if ($domain[0] !== 'localhost'&&$domain[0] !== '127.0.0.1') { //不同域的cookie

           $domain_name=$config['domain_name'];
           @ini_set('session.cookie_domain', $domain_name);//
       }

       //开启以下配置支持session信息存信memcache
       if(c('cache_type')==='memcache'){
           @ini_set("session.save_handler", "memcache");
           $memcache_config=$config['memcache'];
           @ini_set("session.save_path",$memcache_config['host'].':'.$memcache_config['port']);
       }
       else{
           $path=ROOT_PATH.'/data/cache/session';
           if(!is_dir($path)){mk_dir($path,0777,true);}
           session_save_path($path); //去掉文件形式路径
       }

       if(isset($_COOKIE['stoken'])&&$_COOKIE['stoken']){
             session_id($_COOKIE['stoken']);
       }//支持手动设置session_id的值
       $flag= session_start(); //数据输出到浏览器之前调用
       setcookie("stoken",session_id(), time()+7200,null,null,null,true);//两个小时后过期
       return $flag;
   }

    /**
     * controller 调度
     */
  public   function  run(){
         $config=c();

        $act=$config['act'];
       // $act='update';
        $op=$config['op'];
        $act_file =BASE_PATH.'/control/'.$act.'.php';
        $class_name = $act.'Control';

      //如果是indexControl  index.php自动创建文件
      if($act=='index'&&$op=='index'&&!file_exists($act_file)){
          mk_dir(dirname($act_file));
          copy(CORE_PATH.'/tpl/indexcontrol.tpl',$act_file);
      }

        if (!@include($act_file)){
            exit("Base Error: $act_file access file isn't exists!");
        }

        if (class_exists($class_name)){
            $main = new $class_name();
            $function = $op;
            //定义act和op
            if (method_exists($main,$function)){
                $main->$function();
            }else {
                $error = "Base Error: function $function not in $class_name!";
                if(defined('DEBUG')) {
                    throw new Exception($error);
                }
                else{
                    exit($error);
                }
            }
        }else {
            $error = "Base Error: class $class_name isn't exists!";
            exit($error);
        }
    }
  //检测参数是否有plugin参数 plugin=a&method=b  则执行apluin中的b方法，用于测试和plugin的直接处理
  public  function  run_plugin(){
    if(isset($_GET['plugin'])||isset($_POST['plugin'])){
        $plugin=isset($_GET['plugin'])?$_GET['plugin']:$_POST['plugin'];
        $method=isset($_GET['method'])?$_GET['method']:$_POST['method'];
        plugin($plugin)->$method();//指定plugin中的方法
        return true;
    }
      else{
          return false;
      }
  }
}