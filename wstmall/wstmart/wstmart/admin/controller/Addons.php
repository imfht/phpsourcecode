<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Addons as M;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 插件控制器
 */
class Addons extends Base{
	
    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return WSTReturn('',1,$m->pageQuery());
    }
 
    /**
    * 获取数据
    */
    public function get(){
        $m = new M();
        return $m->getById(Input("id/d",0));
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
    * 修改
    */
    public function edit(){
        $m = new M();
        return $m->edit();
    }
    
    /**
     * 设置插件页面
     */
    public function toEdit(){
    	$m = new M();
    	$addon = $m->getById();
    	if(!$addon) $this->error('插件未安装');
    	$addon_class = get_addon_class($addon['name']);
    	if(!class_exists($addon_class)){
    		trace("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
    	}
    	$data = new $addon_class;
    	$addon['addons_path'] = $data->addons_path;
    	$this->meta_title   =   '设置插件-'.$data->info['title'];
    	$db_config = $addon['config'];
    	$addon['config'] = include $data->config_file;
   
    	if($db_config){
    		$db_config = json_decode($db_config, true);
    		foreach ($addon['config'] as $key => $value) {
    			if($value['type'] != 'group'){
    				$addon['config'][$key]['value'] = isset($db_config[$key])?$db_config[$key]:"";
    			}else{
    				foreach ($value['options'] as $gourp => $options) {
    					foreach ($options['options'] as $gkey => $value) {
    						$addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
    					}
    				}
    			}
    		}
    	}
    	$this->assign('data',$addon);
    	$this->assign('addonId',(int)input("id"));
        $this->assign("p",(int)input("p"));
    	return $this->fetch("config");
    }
    
    /**
     * 保存插件设置
     */
    public function saveConfig(){
    	$m = new M();
    	$m->saveConfig();
    	
    	$addon = $m->getById();
    	$addonName = $addon["name"];
    	$class = get_addon_class($addonName);
    	if(!class_exists($class)){
    		return WSTReturn("插件不存在",-1);
    	}
    	$addons = new $class;
    	$addons->saveConfig();
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    
    /**
     * 安装插件
     */
    public function install(){
    	$m = new M();
    	$addon = $m->getById();
    	$addonName = $addon["name"];
        $class = get_addon_class($addonName);
        if(!class_exists($class)){
          	return WSTReturn("插件不存在",-1);
        }
        $addons = new $class;
        $flag = $addons->install();
        if(!$flag){
        	return WSTReturn("安装失败",-1);
        }else{
        	return $m->editStatus(1);
        }
    }
    
    /**
     * 卸载插件
     */
    public function uninstall(){
    	$m = new M();
    	$addon = $m->getById();
    	$addonName = $addon["name"];
    	$class = get_addon_class($addonName);
    	if(!class_exists($class)){
    		return WSTReturn("插件不存在",-1);
    	}
    	$addons = new $class;
    	$flag = $addons->uninstall();
    	if(!$flag){
    		return WSTReturn("卸载失败",-1);
    	}else{
    		return $m->del();
    	}
    	
    }

    /**
     * 启用插件
     */
    public function enable(){
   		$m = new M();
    	$addon = $m->getById();
    	$addonName = $addon["name"];
    	$class = get_addon_class($addonName);
    	if(!class_exists($class)){
    		return WSTReturn("插件不存在",-1);
    	}
    	$addons = new $class;
    	$flag = $addons->enable();
    	if(!$flag){
    		return WSTReturn("启用失败",-1);
    	}else{
    		return $m->editStatus(1);
    	}
    	
    }
    
    /**
     * 禁用插件
     */
    public function disable(){
    	
    	$m = new M();
    	$addon = $m->getById();
    	$addonName = $addon["name"];
    	$class = get_addon_class($addonName);
    	if(!class_exists($class)){
    		return WSTReturn("插件不存在",-1);
    	}
    	$addons = new $class;
    	$flag = $addons->disable();
    	if(!$flag){
    		return WSTReturn("禁用失败",-1);
    	}else{
    		return $m->editStatus(2);
    	}
    	
    }
}
