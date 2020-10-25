<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
 
namespace osc\member\controller;
use osc\common\controller\ModuleInstall;
use think\Db;
class Install extends ModuleInstall{
	
	//此操作只删除数据库表，软删除相关配置，并未删除代码，如有需要请自行处理
	public function uninstall(){
		//删除相关表,16张表
		Db::execute("DROP TABLE " 
		.config('database.prefix'). "member," 
		.config('database.prefix'). "member_auth_group," 
		.config('database.prefix'). "member_auth_group_access,"  
		.config('database.prefix'). "member_auth_rule," 
		.config('database.prefix'). "member_menu,"	
		.config('database.prefix'). "member_wishlist,"	
	
		.config('database.prefix'). "address,"		
		.config('database.prefix'). "cart,"
		.config('database.prefix'). "transport,"
		.config('database.prefix'). "transport_extend,"
		.config('database.prefix'). "order,"
		.config('database.prefix'). "order_goods,"
		.config('database.prefix'). "order_history,"
		.config('database.prefix'). "order_option,"
		.config('database.prefix'). "order_status,"
		.config('database.prefix'). "order_total"
		);		
		//软删除后台相关菜单
		Db::name('menu')->where('module','member')->update(array('status'=>0));
		//软删除相关模块配置
		Db::name('config')->where('module','member')->update(array('status'=>0));
		//软删除模块表中相关信息
		Db::name('module')->where('module','member')->update(array('disabled'=>0));
		
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了member模块');
		//清除缓存
		clear_cache();
		
		$this->success('卸载成功',url('admin/module/index'));
	}
	public function install(){
		
		$module='member';
				
		$return=create_tables($module);
		
		if(isset($return['fail'])){
			
			$this->error($return['fail']);
			
		}elseif(isset($return['success'])){
			
			//更新相关菜单
			if(Db::name('menu')->where('module',$module)->select()){
				Db::name('menu')->where('module',$module)->update(array('status'=>1));
			}
			
			//更新相关系统配置
			if(Db::name('config')->where('module',$module)->select()){
				Db::name('config')->where('module',$module)->update(array('status'=>1));
			}
						
			//更新模块表中相关信息
			Db::name('module')->where('module',$module)->update(array('disabled'=>1,'updatetime'=>date('Y-m-d',time())));
			
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'安装了模块'.$module);
			
			clear_cache();
			
			$this->success($return['success'],url('admin/module/index'));
		}
	}
}
?>