<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/5/22 0022
 * Time: 20:59
 */
 //自己的模板插件化
class TPlugin extends  Plugin{

    /**
     * 单件对象
     */

    private $View = null;
    private  $viewPath='';
    /*
     * 没有构造函数的view ,可以使用多次
     */
    /*plugin 模式**/
    public  function  __construct($path='')
    {
        parent::__construct($path);
        $this->View=new BlitzPhp();
    }

    public    function  load_path($path=''){
        $config=$this->config;

        $path=$config['view_path'].'/'.$path.'.html';
        $this-> View->load("{{include('$path')}}");
    }

    public  function   load($content='')
    {
        $this->View->load($content);
    }

    /*2015-6-17 lcc
     * 模板赋值，可以设置键值key-value或者数组键值，也可以直接设置数组
     * **/
    public   function  set($key,$value='')
    {
        if(is_array($key)){
            $this->View->set($key);
        }
        else{
            $this->View->set(array($key=>$value));
        }
    }

    public     function   display($setting=array())
    {
        $this->View->display($setting?$setting:array());
    }

    public    function  fetch($setting=array())
    {
        return $this->View->  parse($setting?$setting:array());
    }

    public   function  set_global($key,$value='')
    {
        $this->View->set($key,$value);
    }



    /*直接从文件中获取值和变量**/
    public  function  include_file($path,$setting=array())
    {
            $config=$this->config;
         //如果$path以/开头，就从views文件夹开始寻址
        if(substr($path,0,1)=='/'){
            $path=BASE_PATH.'/views'.$path.'.html';
        }
        else{
            $path=$config['view_path'].'/'.$path.'.html';
        }
        if(!$setting){  //没有配置直接输出页面即可
            return file_get_contents($path);
        }
        else{
            $T=new BlitzPhp(); //时间花销很小只有0.3ms左右
            return  $T->Binclude($path,$setting);
        }
    }

    public    function  load_file($path,$setting=array(),$globals=array())
    {
        $config=$this->config;
        $path=$config['view_path'].'/'.$path.'.html';
        $T=new BlitzPhp($path);//独立化模板
        if(is_array($globals)){
            $T->set($globals);
        }
        return  $T->parse($setting);//设置值和返回
    }

    /**
     * lcc 2015-7-5 直接传入内容和设置，获取渲染后的值
     */
    public    function  load_content($content,$setting=array())
    {
        $T=new BlitzPhp();//独立化模板
        $T->load($content);//获取内容
        return  $T->parse($setting);//设置值和返回
    }
    public   function   display_content($content,$setting=array())
    {
        $T=new BlitzPhp();//独立化模板
        $T->load($content);//获取内容
        $T->display($setting);//设置值并直接输出
    }
    public    function   display_file($path,$setting=array())
    {
        $config=$this->config;
        $path=$config['view_path'].'/'.$path.'.html';
        $T=new BlitzPhp($path);//独立化模板
        $T->display($setting);//设置值并直接输出
    }
    /**直接读取文件，包括标签也在其中，并不赋值**/
    public    function  read_file($path){
        $config=$this->config;
        $path=$config['view_path'].'/'.$path.'.html';
        return file_get_contents($path);
    }
    /**
     * 显示页面Trace信息
     *
     * @return array
     */
    public static function showTrace(){
        $trace = array();
        //当前页面
        $trace['当前页面'] =  $_SERVER['REQUEST_URI'].'<br>';
        //请求时间
        $trace['请求时间'] =  date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).'<br>';
        //系统运行时间
        $query_time = number_format((microtime(true)-'StartTime'),3).'s';//应该减去初始时间time
        $trace['页面执行时间'] = $query_time.'<br>';
        //内存
        $trace['占用内存'] = number_format(memory_get_usage()/1024/1024,2).'MB'.'<br>';
        //请求方法
        $trace['请求方法'] = $_SERVER['REQUEST_METHOD'].'<br>';
        //通信协议
        $trace['通信协议'] = $_SERVER['SERVER_PROTOCOL'].'<br>';
        //用户代理
        $trace['用户代理'] = $_SERVER['HTTP_USER_AGENT'].'<br>';
        //会话ID
        $trace['会话ID'] = session_id().'<br>';
        //执行日志
        $log    =   Log::read();
        $trace['sql日志记录']  = count($log)?count($log).'<br/>'.implode('<br/>',$log):'sd';
        $trace['sql日志记录'] = $trace['sql日志记录'].'<br>';
        //文件加载
        $files =  get_included_files();
        $trace['加载文件]'] = count($files).str_replace("\n",'<br/>',substr(substr(print_r($files,true),7),0,-2)).'<br>';
        return $trace;
    }


}