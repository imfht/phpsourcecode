<?php
namespace Home\Controller;

use Home\Controller\HomeController;
use Home\Model\PromotionModel;
use Home\Model\SellerApplyModel;

class SellerController extends HomeController
{
    public function index(){
        $where['allow_apply'] = 1;
        $where['end_time'] = array('gt',NOW_TIME);     
        $promotion = $this->lists(D('Promotion'),$where);
        foreach ($promotion as $k=>$v){
            if($v['end_time'] <=NOW_TIME){
                $promotion[$k]['status'] = 0;//已结束
            }
            if ($v['end_time'] > NOW_TIME && $v['start_time'] <= NOW_TIME){
                $promotion[$k]['status'] = 1;//进行中
            }
            if($v['start_time'] > NOW_TIME){
                $promotion[$k]['status'] = 2;//未开始
            }
        }
        $this->setSiteTitle('活动列表');
        $this->setKeyWords('活动');
        $this->setDescription('活动列表页');
        $this->assign('_list',$promotion);
        $this->display();
    }
    public function apply(){
       
        
        if(IS_POST){
            $pId = I('p_id');
            $SellerApplyModel = new SellerApplyModel();
            if($SellerApplyModel->create()){
                if($SellerApplyModel->add()){
                    $this->success('报名成功，请耐心等待审核');
                }else {
                    $this->error($SellerApplyModel->getError());
                }
            }else {
                $this->error($SellerApplyModel->getError());
            }     
        }else {
            $id = I('id');
            $PromotionModel = new PromotionModel();
            $promotion = $PromotionModel->info($id);
            $this->assign('promotion',$promotion);
            $this->setSiteTitle('商家报名');
            $this->setKeyWords('商家报名');
            $this->setDescription('商家报名页');
            $this->display();
        }
    }
}

?>