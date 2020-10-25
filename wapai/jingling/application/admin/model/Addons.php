<?php
// +----------------------------------------------------------------------
// |   精灵后台系统 [ 基于TP5，快速开发web系统后台的解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 - 2017 http://www.apijingling.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wapai 邮箱:wapai@foxmail.com
// +----------------------------------------------------------------------

namespace app\admin\model;
use think\Model;

/**
 * 插件模型
 * @author wapai   邮箱:wapai@foxmail.com
 */

class Addons extends Model { 
	//关闭自动添加时间到数据库 
	protected $autoWriteTimestamp = false;
    protected $auto = [ 
			'create_time' 
	];
	//修改器，数据赋值自动处理
	protected function setCreateTimeAttr($value) {
		return time ();
	}
	
	/**
	 * 获取插件列表
	 * @param string $addon_dir 插件的地址
	 * @author wapai 邮箱:wapai@foxmail.com       	
	 */
	public function getList($addon_dir = '') {
		if (! $addon_dir)
			$addon_dir = JINGLING_ADDON_PATH;
		//获取插件文件夹所有插件名
		$dirs = array_map ( 'basename', glob( $addon_dir . '*', GLOB_ONLYDIR ) );
		if ($dirs === FALSE || ! file_exists ( $addon_dir )) {
			$this->error = '插件目录不可读或者不存在';
			return FALSE;
		}
		$addons = [];
		$where ['name'] = ['in',$dirs];
		//数据库取出所有的插件
		$list = $this->where ( $where )->field ( true )->select ();
		foreach ( $list as $key => $value ) {
			$list [$key] = $value->toArray ();
		}
		//都设置为未安装
		foreach ( $list as $addon ) {
			$addon ['uninstall'] = 0;
			$addons [$addon ['name']] = $addon;
		}

		foreach ( $dirs as $value ) {
			//未安装
			if (! isset ( $addons [$value] )) {
				$class = get_addon_class ($value);
				if (! class_exists ( $class )) { 
					// 实例化插件失败忽略执行
					\think\Log::record ( '插件' . $value . '的入口文件不存在！' );
					continue;
				}
				$obj = new $class ();
				$addons [$value] = $obj->info;
				if ($addons [$value]) {
					$addons [$value] ['uninstall'] = 1;
					unset ( $addons [$value] ['status'] );
				}
			}
		}

		int_to_string ( $addons, array (
				'status' => array (
						-1 =>'损坏',
						0 => '禁用',
						1 => '启用',
						null => '未安装' 
				) 
		) );
		$addons = list_sort_by ( $addons, 'uninstall', 'desc' );
		return $addons;
	}

    /**
     * 获取插件的后台列表
     * @author wapai 邮箱:wapai@foxmail.com
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
            	$value=$value->toArray(); 
                $admin[] = array('title'=>$value['title'],'url'=>"Addons/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }
}
