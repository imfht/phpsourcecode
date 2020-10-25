<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/13 0013
 * Time: 10:51
 */
//配置函数
class configPlugin  extends Plugin{
  protected   $appConfig=array(); //app和系统的配置，不同于组件的配置
  //加载配置文件
  public function  init(){
      $config=$this->config;
      $config_ini_path=BASE_PATH.'/config/config.ini.php';
      //自动创建config.ini.php
      if(!file_exists($config_ini_path)){
          mk_dir(dirname($config_ini_path));
          //

          $server=$_SERVER['HTTP_HOST'];
          $app_name=basename(BASE_PATH);
           $tpl=$config['plugin_path'].'/tpl/config.ini.tpl';
          $source= str_replace(array('{{server}}','{{app_name}}'),array($server,$app_name),file_get_contents($tpl));

          file_put_contents($config_ini_path,$source);
      }

        //如果有配置文件则加载
         $config=include($config_ini_path);

         if(is_array($config)){
             $this->appConfig=array_merge( $this->appConfig,$config);

         }
  }

  public  function  init_act_op(){

      //post 或get的act和op都存入act和op中
      $act= $_GET['act'] ? strtolower($_GET['act']) : ($_POST['act'] ? strtolower($_POST['act']) : null);
      $op = $_GET['op'] ? strtolower($_GET['op']) : ($_POST['op'] ? strtolower($_POST['op']) : null);
      if (PHP_SAPI === 'cli')//如果是CLI模式
      {
          $argv=$GLOBALS['argv'];
            $act=$argv[1];
            $op=$argv[2];
      }
      /*  这里可以做静态路由解析的工作
      if (empty($_GET['act'])){
          require_once(BASE_CORE_PATH.'/framework/core/route.php');
          new Route($config);
      }
      */
//统一ACTION  默认赋值index
      $act = $act ?  $act : 'index';
      $op=$op? $op : 'index';

      $this->appConfig['act']=$act;
      $this->appConfig['op']=$op;
     return  $act.'|'.$op;
   }
    //配置sql数据库
    public  function  config_db(){
        //$config=c('db');
        $db_config=$this->appConfig['db']['master'];

        Model3::set_link($db_config['dbname'],$db_config['dbprefix'],$db_config['dbhost'],$db_config['dbport'],$db_config['dbuser'],$db_config['dbpwd']);//设置数据库
    }
    //配置sqlit数据库
    public  function  config_sqlit_db(){
        $path=ROOT_PATH.'/separate/'.APP_NAME.'/data/db/'; //先暂时这么写，应该也在配置文件读取数据库路径和文件名一起前缀
        $file_path=$path.'/meirong.db';
        if(!class_exists('ModelLit',false)){
            lib('ModelLit');
        }
        if(file_exists($file_path)){//可以进行操作
            ModelLit::set_link($file_path,'m_');//配置数据连接为本地连接,当其它地方也是用sqli时可能引起混淆，请注意
        }
        else{
            //没有数据库需要进行创建数据库和表结构，没有会自行创建数据库
            die("Db Error: sqlit connect failed");
        }
     }

  //外部调用获取配置函数
  public  function  c($key='',$value=null){

         if($value!==null&&$key){
             $this->appConfig[$key]=$value;
         }
       else{
           if($key){
               return  $this->appConfig[$key];
           }
           else{
               return  $this->appConfig;
           }
       }
    }

}
//获取config中的配置
function c($key='',$value=null){
   return plugin('core/config')->c($key,$value);
}