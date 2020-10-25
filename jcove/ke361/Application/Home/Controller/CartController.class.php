<?php
namespace Home\Controller;

use Home\Controller\HomeController;
use Home\Model\GoodsModel;
use Home\Model\CartModel;
use Think\Model;

class CartController extends HomeController
{
    public function add(){
        if(empty($this->my)){
            $this->error('请先登录');
        }
        $id = I('id',0,'intval');
        if($id <=0 ){
            $this->error('商品无效');
        }else {
            $CartModel = new CartModel();
            if($CartModel->addToCart($id)){
                $this->success('添加成功');
            }else{
                $this->error($CartModel->getError());
            }
        }
       
        
    }
    public function cart(){
        if(empty($this->my)){
            $this->error('请先登录');
        }
        $CartModel = new CartModel();
        $where['uid'] = UID;
        $cartList = $this->listAll($CartModel,$where);
        $GoodsModel = new GoodsModel();
        $total = array();
        foreach ($cartList as $k=>$v){
            $goods = $GoodsModel->info($v['goods_id']);
            $cartList[$k]['pic_url'] = $goods['pic_url'];
            $cartList[$k]['name']   = $goods['name'];
            
            $cartList[$k]['subtotal']= $goods['price']*$v['number'];
            if($goods['goods_type'] == '1'){
                $cartList[$k]['subtotal_format'] = number_format($cartList[$k]['subtotal'],2);
                $cartList[$k]['price']   = $goods['price'];
                if($cartList[$k]['status']>0){
                    $total['total']+=$cartList[$k]['subtotal'];
                }

            }
            if($goods['goods_type'] == '2'){
                $cartList[$k]['subtotal_format'] = $cartList[$k]['subtotal'].'积分'; 
                $cartList[$k]['price']   = number_format($goods['price']).'积分';
                if($cartList[$k]['status']>0) {
                    $total['score'] += $cartList[$k]['subtotal'];
                }
            }

            
        }
        $total = $total['score'].'积分+￥'.number_format($total['total'],2);
        $this->assign('total',$total);
        if(empty($cartList)){
            $this->error('购物车为空');
        }
        $this->assign('_list',$cartList);
        
        $this->setSiteTitle('购物车');
        $this->display();
    }
    public function del(){
        if(empty($this->my)){
            $this->error('请先登录');
        }
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('Cart')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
    public function changeStatus(){
        if(empty($this->my)){
            $this->error('请先登录');
        }
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        $ids= I('ids','');
        if(!empty($ids)){
            $id = $ids;
        }
        $check = I('check');

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $CartModel = new CartModel();
        $where['id'] =   array('in',$id);
        $cartGoods = $CartModel->where($where)->select();
        foreach ($cartGoods as $k=>$v){
            $map['id'] = $v['id'];
            $data['status'] = $check=='true' ? 1:0;

            $CartModel->where($map)->save($data);

        }
        $total = $CartModel->total();
        $total = '合计:'.$total['score'].'积分+￥'.$total['total'];
        $this->ajaxReturn($total);
    }
    public function updateCart(){
        $result['errno'] = 1;
        $result['count'] = 0;
        if(UID){
            $where['uid'] = UID;
            $CartModel = new CartModel();
            $count = $CartModel->where($where)->count('id');
            if(intval($count)>0){
                $result['errno'] = 0;
                $result['count'] = $count;
            }
            
        }
        
       
        $this->ajaxReturn($result);
    }
}

?>