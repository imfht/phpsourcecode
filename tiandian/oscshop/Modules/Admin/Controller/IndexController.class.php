<?php
namespace Admin\Controller;
use Admin\Model\StatisticsModel;
class IndexController extends CommonController {
   	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='首页';
			$this->breadcrumb2='首页';
	}
    public function index(){
    	
		$model=new StatisticsModel();
		
       	$this->total_ip=count($model->get_all_visitors_ip());
		$this->today_ip=count($model->get_visitors_ip_by_date(date('Y-m-d',time())));
		
		$this->total_member=count($model->get_all_member());
		$this->today_member=count($model->get_today_register_member());
		
		$this->total_money=$model->get_total_sales();
		$this->today_money=$model->get_total_sales(array('date_added' => date('Y-m-d')));
		
		$this->total_order=$model->get_total_order();
		$this->today_order=$model->get_total_order(array('date_added' => date('Y-m-d')));
		
		$order_model=new \Admin\Model\OrderModel();   
		
		$data=$order_model->show_order_page();		
		
		$this->empty=$data['empty'];
		$this->list=$data['list'];		
		
		$this->uc_empty='~~暂无数据';
		$this->uc_list=$model->get_user_action();
		
        $this->display();
	}
}