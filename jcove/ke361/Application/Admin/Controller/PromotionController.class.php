<?php
namespace Admin\Controller;

use Admin\Controller\AdminController;
use Admin\Model\PromotionModel;
use Admin\Model\GoodsModel;

class PromotionController extends AdminController
{
    public function index(){
        $promotion = $this->lists(D('Promotion'));
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
        $this->assign('_list',$promotion);
        $this->display();
    }
    public function edit(){
        $id = I('id');
        $PromotionModel = new PromotionModel();
        if(IS_POST){
            if(is_numeric($id) && $id >0 ){
                $where['id'] = $id;
                $PromotionModel->create();
                if($PromotionModel->where($where)->save()){
                    $this->success('操作成功');
                }else {
                     
                    $this->error('操作失败');
                }
            }else{
                $PromotionModel->create();
                if($PromotionModel->add()){
                    $this->success('添加成功');
                }else {
                    $this->success('添加失败');
                }
            }
        }else{
            if(isset($id)){
                $where['id'] = $id;
              
                $this->assign('promotion',D('Promotion')->where($where)->find());
            }
            $this->display();
        }
    }
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('Promotion')->where($where)->delete()){
            $this->success('删除成功',U('index'));
        }else {
            $this->error('删除过程中遇到错误');
        }
    
    }
    public function sellerApply(){
        $sellerApply = $this->lists(D('SellerApply'));
        $PromotionModel = new PromotionModel();
        foreach ($sellerApply as $k=>$v){
            $promotion = $PromotionModel->info($v['id']);
            $sellerApply[$k]['promotion_name'] = $promotion['promotion_name'];
        }
        $this->assign('_list',$sellerApply);
        $this->display();
    }
    public function delApply(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('SellerApply')->where($where)->delete()){
            $this->success('删除成功',U('index'));
        }else {
            $this->error('删除过程中遇到错误');
        }
    
    }
}

?>