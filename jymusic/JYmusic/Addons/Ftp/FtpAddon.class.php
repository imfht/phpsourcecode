<?php

namespace Addons\Ftp;
use Common\Controller\Addon;
use Think\Db;
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

class FtpAddon extends Addon{
 	
    public $info = array(
        'name'=>'Ftp',
        'title'=>'FTP管理',
        'description'=>'Ftp 扫描',
        'status'=>1,
        'author'=>'JYmusic',
        'version'=>'0.1'
    );
    
    public $addon_path = './Addons/ftp/';
    
    /**
     * 配置列表页面
     * @var unknown_type
     */
    public $admin_list = array(
    		'listKey' => array(
    				'title'=>'目录名称',
    				'typetext'=>'文件名',
    				'statustext'=>'大小',
    				'level'=>'优先级',
    				'create_time'=>'开始时间',
    		),
    );
    public $custom_adminlist = 'adminlist.html';

    /**
     * (non-PHPdoc)
     * 安装函数
     * @see \Common\Controller\Addons::install()
     */
    public function install(){
        return true;
    }

    /**
     * (non-PHPdoc)
     * 卸载函数
     * @see \Common\Controller\Addons::uninstall()
     */
    public function uninstall(){
        return true;
    }                      
        
}