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
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class LengthClass extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','商品');
		$this->assign('breadcrumb2','重量单位');
	}
	
    public function index(){     	
		
		$list = Db::name('length_class')->paginate(config('page_num'));
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
		$this->assign('list', $list);
	
		return $this->fetch();

	 }
	 public	function add(){
		if(request()->isPost()){	
			return $this->single_table_insert('LengthClass','添加了重量单位');
		}
		$this->assign('crumbs', '新增');
		$this->assign('action', url('LengthClass/add'));
		return $this->fetch('edit');
	}
	 public	function edit(){
		if(request()->isPost()){	
			return $this->single_table_update('LengthClass','修改了重量单位');
		}
		$this->assign('crumbs', '修改');
		$this->assign('action', url('LengthClass/edit'));		
		$this->assign('d',Db::name('LengthClass')->find(input('id')));		
		return $this->fetch('edit');
	}
	public	function del(){
		if(request()->isGet()){	
			$r= $this->single_table_delete('LengthClass','删除了重量单位');
			if($r){
				$this->redirect('LengthClass/index');
			}
		}
	}
	 
}
?>