<?php
namespace addon\superlinks\controller;
use app\common\controller\AddonBase;
use addon\superlinks\logic\Superlinks as LogicSuperlinks;

class Superlinks extends AddonBase{
	
	
	private static $superlinksLogic = null;
	
	/**
	 * 构造方法
	 */
	public function _initialize()
	{
	
		parent::_initialize();
	
		self::$superlinksLogic = get_sington_object('superlinksLogic', LogicSuperlinks::class);
	}
	
	/* 添加友情连接 */
	public function superlinksAdd(){
		
		
		
		IS_POST && $this->jump(self::$superlinksLogic->superlinksAdd($this->param));
		return $this->addonTemplate('/superlinks_add');
		
		
	
	}
	
	/* 编辑友情连接 */
	public function superlinksEdit(){
		
		$info = self::$superlinksLogic->getSuperlinksInfo(['id' => $this->param['id']]);
		
		IS_POST && $this->jump(self::$superlinksLogic->superlinksAdd($this->param));
		
		$this->assign('info', $info);
		
		return $this->addonTemplate('/superlinks_edit');
		
	}
	/* 禁用友情连接 */
	public function superlinksForbidden(){
		
		$this->jump(self::$superlinksLogic->setSuperlinksValue(['id'=>$this->param['id']],'status',0,'友情链接禁用成功'));
		
		
		
	}
	
	/* 启用友情连接 */
	public function superlinksOff(){
		
		$this->jump(self::$superlinksLogic->setSuperlinksValue(['id'=>$this->param['id']],'status',1,'友情链接启用成功'));
	}
	
	/* 删除友情连接 */
	public function superlinksDel(){
		$this->jump(self::$superlinksLogic->superlinksDel(['id' => $this->param['id']]));
	}
	/* 批量删除友情连接 */
	public function superlinksAlldel(){
		$this->jump(self::$superlinksLogic->superlinksAlldel($this->param['ids']));
	}	
}
