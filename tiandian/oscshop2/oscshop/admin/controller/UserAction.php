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
class UserAction extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','系统');
		$this->assign('breadcrumb2','用户行为');
	}
	
    public function index()
    {
    	
    	$list = Db::name('user_action')->order('ua_id desc')->paginate(config('page_num'));
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
		$this->assign('list',$list);
		    
		return $this->fetch();   
    }

	
}
