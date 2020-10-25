<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/12 0012
 * Time: 16:58
 *
 * 模拟js实现php的插件机制，实现插件机制的目的是代码复用，能够将常见的数据处理模块化，组件化执行
 * 整个页面逻辑就是数据流的机制，仿照js的事件机制原理
 */
//类似于jquery ,能够创建一个插件对象，包含流程默认的一些事件
class Plugin{
    protected   $config=array();//组件基本配置 使用子组件的配置
    protected   $Hooks=array();//钩子函数，用于存储绑定的事件等

    //参数传递当前加载子插件的路径
    function __construct($pluginfile='') {
        $plugin_path=dirname($pluginfile);
        $config_path=$plugin_path.'/config.php';

             if(file_exists( $config_path)){
                $config=include_once($config_path);
                 if(is_array($config)){
                     $this->config=$config;//加载默认的配置文件
                 }
             }
        $this->config['plugin_path']=$plugin_path;
    }
   //获取绑定的事件和消息
    public  function  get_hook(){
         return $this->Hooks;
    }
   //绑定事件
    public  function  on($event,$listener){
        $Hook=$this->Hooks;
        if(!isset($Hook[$event])){ //如果之前没有绑定
            $Hook[$event]=array();//赋值一个空数组
        }
        $index=array_push($Hook[$event],$listener)-1;//返回事件的索引
        $this->Hooks=$Hook;
        return  array('event'=>$event,'index'=>$index,'listener'=>$listener);

    }
    //解除绑定事件 参数为on事件的返回值
    public  function  off($setting=array()){
           $event=$setting['event'];
           $index=$setting['index'];
         unset ($this->Hooks[$event][$index]);
    }
   //触发事件 最多可以传递两个参数
    public  function  trigger($event,$param1=null,$param2=null){

        if(isset($this->Hooks[$event])){
            $listener=$this->Hooks[$event];
            foreach($listener as $value){
                //有可能$value是数组，
                if(is_array($value)){  //是数组的组件
                   //实例化plugin
                    foreach($value as $key2=>$value2){
                        if(strpos($value2,'->')>0){ //如果存在箭头说明也是触发函数
                            $arr=explode('->',$value2);
                            $key2=trim($arr[0]);
                            $value2=trim($arr[1]);
                        }

                        //触发
                         $plugin= plugin($key2);
                         $plugin->trigger('on_'.$value2,$param1,$param2); //数执行前触发函
                         $return=$plugin->$value2($param1,$param2);
                         $plugin->trigger('end_'.$value2,$return,$param1); //返回值传入结束函数中，函数执行后触发
                    }
                    //触发函数
                }
                else{  //是匿名函数的事件绑定
                    $value($param1,$param2);  //直接执行按照添加顺序监听的代码
                }

            }
        }
    }
    //触发自身的实际函数
    public  function  trigger_method($method,$param1=null,$param2=null){
           $this->$method($param1=null,$param2=null);
    }
    //子类组件公用函数
    //处理配置 用户基础配置与传入的配置或者参数合并
    protected   function  _config($config){
        if($config){
            $config=array_merge($this->config,$config);//新配置会覆盖默认的配置
            $this->config=$config;
        }
        else{
            $config=$this->config;
        }
        return $config;
    }
    //获取当前插件的配置
    public   function  c($key='',$value=null){
        if($value!==null){//设置值
            $this->config[$key]=$value;
        }else{
            //读取值
            if($key){
                return  $this->config[$key];
            }
            else{
                return  $this->config;
            }
        }
    }
}

//第二个参数代表是否从  app/plugin的格式，无app/则默认为当前app;  添加初始化即有加载配置的行为父类construct加载
function plugin($load_plugin,$pre_callback=null){
     if(strpos($load_plugin,'/')){//包含区分组件的情况,不包括开始就是/的情况
        $arr=explode('/',$load_plugin);
         $app=$arr[0];
         $plugin=$arr[1];
         $file_name = ROOT_PATH .'/'.$app.'/plugins/' .$plugin.'/'. $plugin . '.php';
     }
    else{
        $plugin=$load_plugin;
        //先从core中找，没有的话再从本地plugin中找
        $file_name = CORE_PATH . '/plugins/' .$plugin.'/'. $plugin . '.php';
        if(!file_exists($file_name)){
        $file_name = BASE_PATH . '/plugins/' .$plugin.'/'. $plugin . '.php'; //向上兼容
        }
        $load_plugin=basename(BASE_PATH).'/'.$plugin;//为了保证键值一致性，添加app前缀。否则可能声明两个对象
    }

    static $_cache = array();
    static $pre_hook=array();  //预挂载事件，用于初始化执行，主要解决配置只有一个位置正确，且初始化一次的问题
    if($pre_callback){
        $pre_hook[$load_plugin]=$pre_callback;
        return true;
    }
    if (isset($_cache[$load_plugin])) {
        return $_cache[$load_plugin];
    }

    if (!$plugin) //如果为空或null直接返回model对象
    {var_dump($plugin);
        throw new Exception('no plugin param find');  //没有插件对象，更容易发现错误
      //  return new Plugin($file_name);
    }
    $class_name =$plugin . 'Plugin';
    //重新生成一个新对象，已经引入过文件
    if (class_exists(@$class_name, false)) { //由于测试是indexControl已经加载过但不是这个方法加载过的，所以没在静态缓存中
        $_cache[$load_plugin] = new $class_name($file_name);
       //触发初始化函数
        if(isset($pre_hook[$load_plugin])&&$pre_hook[$load_plugin]){
            $pre_hook[$load_plugin]($_cache[$load_plugin],$plugin);
        }
        return $_cache[$load_plugin];
    }
    include($file_name); //动态引入文件
    if (!class_exists($class_name)) {
        $error = 'Plugin Error:  Plugin ' . $class_name . ' is not exists!';
        throw new Exception($error);
    } else {
        $_cache[$load_plugin] = new $class_name($file_name);
        if(isset($pre_hook[$load_plugin])&&$pre_hook[$load_plugin]){
            $pre_hook[$load_plugin]($_cache[$load_plugin],$plugin);
        }
        return  $_cache[$load_plugin];
    }
}

//判断组件是否存在
function plugin_exist($load_plugin){
    if(strpos($load_plugin,'/')){//包含区分组件的情况,不包括开始就是/的情况
        $arr=explode('/',$load_plugin);
        $app=$arr[0];
        $plugin=$arr[1];
        $file_name = ROOT_PATH .'/'.$app.'/plugins/' .$plugin.'/'. $plugin . '.php';
    }
    else{
        $plugin=$load_plugin;
        //先从core中找，没有的话再从本地plugin中找
        $file_name = CORE_PATH . '/plugins/' .$plugin.'/'. $plugin . '.php';
        if(!file_exists($file_name)){
            $file_name = BASE_PATH . '/plugins/' .$plugin.'/'. $plugin . '.php'; //向上兼容
        }
        $load_plugin=basename(BASE_PATH).'/'.$plugin;//为了保证键值一致性，添加app前缀。否则可能声明两个对象
    }
    if(file_exists($file_name)){
        return true;
    }
    else{
        return false;
    }
}

//自动加载前缀的加载组件
function xPlugin($load_plugin,$pre_callback=null){
  return  plugin(c('plugin_prefix').$load_plugin,$pre_callback);
}

//升级或者复制组件到当前系统中的plugin
function upgrade_plugin($plugin){
  $up_arr=include(CORE_PATH.'/tpl/plugin.tpl');
  $path= $up_arr['path'];
  if(is_dir($path.'/'.$plugin)){
      $target=CORE_PATH.'/plugins/'.$plugin;
       mk_dir($target);
      copy_dir($path.'/'.$plugin,$target);
      echo $plugin.' upgrade success!!';
  }
  else{
     echo $plugin.' not in plugin.tpl';
 }
}