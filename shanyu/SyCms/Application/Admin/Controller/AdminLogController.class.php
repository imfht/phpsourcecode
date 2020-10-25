<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AdminLogController extends AdminBaseController {

	public function index(){
		//搜索
		$where = $this->_search();

		//分页
		$limit = $this->_page('AdminLog',$where);

		//数据
		$list=M('AdminLog')
			->limit($limit)
			->where($where)
			->order('id desc')
			->select();
		$this->assign('list',$list);

		$this->display();
	}

	public function delByMonth($month){
		$month=I('month',3,'intval');
		$status=M('AdminLog')->where("log_time <= DATE_SUB(now(),INTERVAL {$month} MONTH)")->delete();
		if($status) $this->success('清理成功');
		else $this->error('清理失败');
	}








}