<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间APP基础控制器
 */
namespace Space\Controller;
use Common\Controller\FrontendController;

class SpaceBaseController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		//生成导航
		$this->assign('arrNav',$this->_pagenav());
	}
	/*
	 * 个人空间导航 二期完善 配置成后台可以更新的导航 暂时先这样
	 * */
	protected  function _pagenav(){
		$arrNav = array ();
		if($this->visitor){
			$arrNav['update'] = array('name'=>'动态广播', 'url'=>U('update/index'));
			$arrNav['index']  = array('name'=>'我的空间', 'url'=>U('index/index',array('id'=>$this->visitor['doname'])));

		}else{
			$arrNav['index'] = array('name'=>'首页', 'url'=>C('ik_site_url'));		
		}
		$arrNav['groups']  = array('name'=>'发现小组', 'url'=>U('group/explore/groups'));
		$arrNav['topics']  = array('name'=>'发现话题', 'url'=>U('group/explore/topics'));
		return $arrNav;
	}
	/**
	 * 瀑布显示
	 */
	public function waterfall($where = array(), $order = 'photoid DESC', $page_max = '') {
		$spage_size = 10; //每次加载个数
		$spage_max = 10; //每页加载次数
		$page_size = $spage_size * $spage_max; //每页显示个数
	
		$item_mod = D('UserPhoto');
		$count = $item_mod->where($where)->count('photoid'); 
		//控制最多显示多少页
		if ($page_max && $count > $page_max * $page_size) {
			$count = $page_max * $page_size;
		}
		//分页
		$pager = $this->_pager($count, $page_size);
		$arrphoto = $item_mod->field('photoid')->where($where)->order($order)->limit($pager->firstRow.','.$spage_size)->select();
		
		foreach ($arrphoto as $val) {
			$item_list[] = $item_mod->getOnePhoto($val['photoid']);
		}
		$this->assign('item_list', $item_list);
		//当前页码
		$p = $this->_get('p', 'intval', 1);
		$this->assign('p', $p);
		//当前页面总数大于单次加载数才会执行动态加载
		if (($count - ($p-1) * $page_size) > $spage_size) {
			$this->assign('show_load', 1);
		}
		//总数大于单页数才显示分页
		$count > $page_size && $this->assign('page_bar', $pager->show());
		//最后一页分页处理
		if ((count($item_list) + $page_size * ($p-1)) == $count) {
			$this->assign('show_page', 1);
		}
	}
	/**
	 * 瀑布加载
	 */
	public function wall_ajax($where = array(), $order = 'photoid DESC') {
	
		$spage_size = 10; //每次加载个数
		$spage_max = 10; //加载次数
		$p = $this->_get('p', 'intval', 1); //页码
		$sp = $this->_get('sp', 'intval', 1); //子页
	
		//条件
		//计算开始
		$start = $spage_size * ($spage_max * ($p - 1) + $sp);
		$item_mod = D('UserPhoto');
		$count = $item_mod->where($where)->count('photoid');

		$arrphoto = $item_mod->field('photoid')->where($where)->order($order)->limit($start.','.$spage_size)->select();
		foreach ($arrphoto as $key=>$val) {
			$item_list[] = $item_mod->getOnePhoto($val['photoid']);
		}
		$this->assign('item_list', $item_list);
		$resp = $this->fetch('Public:waterfall');
		$data = array(
				'isfull' => 1,
				'html' => $resp
		);
		$count <= $start + $spage_size && $data['isfull'] = 0;
		$this->ajaxReturn(array(
				'status' => 1,
				'msg' => '',
				'data' => $data
		));
	
	}		
}