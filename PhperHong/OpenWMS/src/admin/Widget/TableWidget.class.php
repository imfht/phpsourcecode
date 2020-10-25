<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Widget;
use Think\Controller;
class TableWidget extends Controller{
	
	protected $file = 'default';
	
	protected $compent = null;
	/**
	+----------------------------------------------------------
	* 设置插件模板
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	*/
	public function setTemplateFile($file){
		if(!empty($file)){
			$this->file = $file;	
		}	
	}
	/**
	+----------------------------------------------------------
	* 渲染模板 
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	*/	
	public function render($data){

		$data['obj'] = $this;
		$this->assign($data);

		$this->display('Widget/table');
	}
	/**
	+----------------------------------------------------------
	* 设置装饰者
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	*/	
	public function setCompent($compent){
		if(!empty($compent)){
			$this->compent = $compent;	
		}
	}
	/**
	+----------------------------------------------------------
	* 魔法方法
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	*/
	public function __call($name,$args){
		
		if(!is_null($this->compent)){

			$r = new \ReflectionClass($this->compent);
			if(!$r->hasMethod($name)){
				return '';	
			}
			if($method = $r->getMethod($name)){
				if($method->isPublic() && !$method->isAbstract()){
					return $method->invoke($this->compent,$args);
				}	
			}	
		}
		return '';	
	}
}
?>