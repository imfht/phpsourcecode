<?php
// +----------------------------------------------------------------------
// | eventBindPhpFrame [ keep simple try auto ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2016 eventBindPhpFrame All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yannanfei <yannanfeiff@126.com>
// +----------------------------------------------------------------------

class resourcePlugin extends  Plugin
{
   //新的初始化
    public  function  init(){

        $plugin_path=$this->c('plugin_path');
        $root_path=ROOT_PATH;
        $base_path=BASE_PATH;

        if(!file_exists($root_path.'/plugins/util')){
            upgrade_plugin('util');//强制依赖的插件
        }
        if(!file_exists($root_path.'/plugins/T')){
            upgrade_plugin('T');//强制依赖的插件
        }

        $root_copy=array('bootstrap3','jquery','seajs','util','partjs');
        $base_copy=array('resource/common/css','resource/common/js','resource/common/images','views','design_view','control');
        $file_copy=array(
            'config.js.tpl'=>'resource/common/js/config.js',
            'demo.js.tpl'=>'resource/common/js/demo.js',
            'control.tpl'=>'control/demo.php',
            'design_demo.tpl'=>'design_view/demo.html',
            'design_layout.tpl'=>'design_view/layout.html',
            'layout.tpl'=>'views/layout.html',
            'view_demo.tpl'=>'views/demo.html',
        );
   //查看core/resource是否有bootstrap3,jquery,seajs,util 文件夹，没有则创建和复制sea.init.js
   foreach($root_copy as $value){
       $folder=$root_path.'/resource/'.$value;
       if(!file_exists($folder)){//如果不存在文件夹说明资源还未加载
         upgrade_resource($value);
       }
   }
//查看base目录resource是否有common/css,images,js 文件夹，没有则创建
   foreach($base_copy as $value){
       $folder=$base_path.'/'.$value;
       if(!file_exists($folder)){//如果不存在文件夹说明资源还未加载
           mk_dir($folder);
       }
   }
        //复制文件
        //在js文件夹中创建config.js  demo.js
   foreach($file_copy as $key=> $value){
       $source=$plugin_path.'/tpl/'.$key;
       $target=$base_path.'/'.$value;
       if($key=='config.js.tpl'){
           $this->check_update_config(true); //强制更新生成
       }
       else{
           copy($source,$target);
       }
   }
        //将初始化语句替换掉
        $path= $base_path.'/index.php';
        //  preg_match("~plugin\('.+?resource'\)->init\(\);~",file_get_contents($path),$matches);
        // var_dump($matches);die;
        //index替换init
        $index_content=file_get_contents($path);
        $index_content=str_replace("plugin('resource')->init();","//plugin('resource')->init();",$index_content);
        $index= preg_replace("~(plugin\('.+?resource'\)->init\(\))~","//\\1 visit:index.php?act=demo",$index_content);
        file_put_contents($path,$index);
        echo '<br/>resource init finish <br/> visit:<a href="index.php?act=demo">演示示例</a>';

    }

  //初始化
   public  function  old_init(){

     //所有目录基于base_url
     $config=$this->config;
     $folder_structure=$config['folder_structure'];
     $base_folder=BASE_PATH;
    //创建目录
    $this->create_folder($folder_structure,$base_folder);
       //复制文件
     $this->copy_resource();
   //将初始化语句替换掉
      $path= $base_folder.'/index.php';
     //  preg_match("~plugin\('.+?resource'\)->init\(\);~",file_get_contents($path),$matches);
      // var_dump($matches);die;
       //index替换init
       $index_content=file_get_contents($path);
       $index_content=str_replace("plugin('resource')->init();","//plugin('resource')->init();",$index_content);
     $index= preg_replace("~(plugin\('.+?resource'\)->init\(\))~","//\\1 visit:index.php?act=demo",$index_content);
       file_put_contents($path,$index);
      echo '<br/>resource init finish <br/> visit:<a href="index.php?act=demo">演示示例</a>';
    }
    //递归创建folder
    private  function  create_folder($folder,$base_path){
        foreach($folder as $key=>$value){
            if(is_array($value)){//有子集为数组说明有下级目录，以key创建上级目录
                mk_dir($base_path.'/'.$key);
                $this->create_folder($value,$base_path.'/'.$key);
            }
            else{ //没有下级目录，以value创建本级目录
                mk_dir($base_path.'/'.$value);
            }
        }
    }
    //复制demo资源到指定目录
   private  function  copy_resource(){
    //必须要的文件
       $config=$this->config;
       $plugin_path=$config['plugin_path'];
       $files=$config['file_copy'];
        $source_folder=$plugin_path.'/tpl/';
       $base_folder=BASE_PATH;
      foreach($files as $key=>$value){
          //if(strpos($value,'.')>0){  }//复制到文件
          //暂时支持复制文件
          $target=$base_folder.'/'.$value;
          $target_folder=dirname($target);
          $source=$source_folder.$key;
          if(!file_exists($target)){
              mk_dir($target_folder);
            //  echo $source;die;
             //config.js比较特殊，
              if($key=='config.js.tpl'){
                 $this->check_update_config(true); //强制更新生成
              }
              else{
                  copy($source,$target);
              }

          }
      }
      $folder=$config['folder_copy'];
       foreach($folder as $key=>$value){
           if(is_dir($key)){
              copy_dir($key,$base_folder.'/'.$value.'/'.basename($key));
           }
           else{
               echo '【' .$key.'】 is not exist';
           }
       }

   }

    //为了使配置文件config.js与后台config.ini.php项同步，需要做修改检测，如果检测修改过config.ini.php要自动修改config.js
    //参数为是否强制更新
    public  function  check_update_config($flag=false){
        $config=$this->config;
        $cache_key='configjs';
        $cache_path= $plugin_path=$this->c('plugin_path').'/cache';
        if($config['config_js_tpl_path']){
            $config_template=$config['config_js_tpl_path'];
        }
        else{
            $config_template=$config['plugin_path'].'/'.'tpl/config.js.tpl';
        }

     //util组件已经可以使用
     $plugin= plugin('util');

      $alter_stamp=  $plugin->get_cache($cache_key,$cache_path);//存储config.ini.php和config.js模板文件的修改时间戳
      $time_stamp_arr=explode('|',$alter_stamp);

        //对比上次修改时间和文件自身时间
        $config_path=BASE_PATH.'/config/config.ini.php';
        $stat_configIni=stat($config_path);
        $stat_jsTpl=stat($config_template);

        if($stat_configIni){ //config.ini.php文件存在的情况

            //双向修改判断都会更改config.js
            if($flag||!$alter_stamp||$stat_configIni['mtime']>$time_stamp_arr[0]||$stat_jsTpl['mtime']>$time_stamp_arr[1]){ //没有创建的情况或者有改动直接赋值覆盖，双向改动和覆盖

                //重新生成config.js文件
                   $config=c('');//获取所有配置
                   $T=new BlitzPhp();
                  $config=$T->Binclude($config_template,$config);  //路径解决-----------

                   $target_js=BASE_PATH.'/resource/common/js/config.js';
                   file_put_contents($target_js,$config);
                  //获取新的时间戳存入
                $stat_configIni=stat($config_path);
                $stat_jsTpl=stat($config_template);
                $times=$stat_configIni['mtime'].'|'. $stat_jsTpl['mtime'];
                $plugin->set_cache($cache_key,$times,$cache_path);
             }
        }
    }


}