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
return array(
    //要创建的文件目录结构
   'folder_structure'=>array(
       'control',
       'resource'=>array('common'=>array('js','css','images'),'jquery','seajs'),
       'views',
       'plugins',
   ),
    //如果文件已经存在就不进行复制操作
    'file_copy'=>array( //文件复制  键值默认去掉了tpl文件夹和tpl后缀名,value不加后缀名默认是复制到文件夹，加上后缀名默认复制为文件
        'config.js.tpl'=>'resource/common/js/config.js',
        'demo.js.tpl'=>'resource/common/js/demo.js',
        'sea.ini.tpl'=>'resource/common/js/sea.init.js',
        'jquery.min.tpl'=>'resource/jquery/jquery.min.js',
        'sea.tpl'=>'resource/seajs/seajs.js',
        'demo.html.tpl'=>'views/demo.html',
        'control.tpl'=>'control/demo.php'
    ),
    //文件夹复制，主要是插件之类的插件
    'folder_copy'=>array(
      '升级目录'=>'plugins',  //T模板插件
      '升级目录-，'=>'plugins', //功能函数插件  后台插件
      '升级目录--'=>'resource', //功能函数插件 前台插件
     ),
     'config_js_tpl_path'=>''  //配置js模板路径，用于缓存修改，可以自定义设置


);