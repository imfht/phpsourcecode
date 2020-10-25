<?php

namespace Addons\FileManager;
use Common\Controller\Addon;

/**
 * 文件管理插件
 * @author 翟小斐
 */

    class FileManagerAddon extends Addon{

        public $info = array(
            'name'=>'FileManager',
            'title'=>'文件管理',
            'description'=>'后台上传文件管理',
            'status'=>1,
            'author'=>'翟小斐',
            'version'=>'1.0'
        );

        public $admin_list = array(
            'model'=>'File',		//要查的表
			'fields'=>'*',			//要查的字段
			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
			'order'=>'id desc',		//排序,
			'list_grid'=>array( 		
                'id:ID',
			    'name:文件名',
                'savename:文件存储名',
                'size:文件大小',
                'location:物理路径',
                'url:链接',
                'create_time|time_format:上传时间',       
                'id:操作:[EDIT]|编辑,[DELETE]|删除'
            ),
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }


    }