<?php
namespace Home\Controller;

use Home\Controller\HomeController;
use Home\Model\CartModel;
use Home\Model\GoodsModel;
use Home\Model\UserAddressModel;
use Admin\Controller\PublicController;
use Home\Model\OrderModel;
use Home\Model\MemberModel;
use Home\Model\OrderGoodsModel;

class BuyController extends HomeController
{
    private $total = array(
            'score'=>0,
            'total' =>0,
        ); 
    private $cartList = array();
  
    public function cart(){
        $this->initCart();
       
        $UserAddressModel = new UserAddressModel();
        $address = $UserAddressModel->addressList();
        $this->assign('user_address',$address);
        $this->assign('total',$this->total);
        $this->assign('_list',$this->cartList);
        $this->setSiteTitle('确认订单');
        $this->display();
    }
    public function submit(){
        $score = $this->my['score'];
        $this->initCart();
        if($this->total['score'] > $score){
            $this->error('积分不足已支付订单');
        }
        $userAddressId = I('post.address_id');
        $userAddress   = D('UserAddress')->info($userAddressId); 
        $order = array(
            'goods_amount'  =>$this->total['total'],
            'use_score'    =>$this->total['score'], 
            'consignee'     =>$userAddress['consignee'],
            'province'      =>$userAddress['province'],
            'city'          =>$userAddress['city'],
            'district'      =>$userAddress['district'],
            'community'     =>$userAddress['community'],
            'address'       =>$userAddress['address'],
            'mobile'        =>$userAddress['mobile'], 
            'status'        =>1
        );
        $OrderModel = new OrderModel();
        if($OrderModel->addOrder($order)){
            $MemberModel = new MemberModel();
            $MemberModel->setScore(UID,$score-$this->total['score']);
            $orderId = $OrderModel->getLastInsID();
            foreach ($this->cartList as $row){
                $goods = array(
                    'goods_id'  => $row['goods_id'],
                    'number'    => $row['number'],
                    'goods_type'=> $row['goods_type'],
                    'price'=>$row['price'],
                    'goods_name' =>$row['name'],
                    'pic_url'    =>$row['pic_url'],
                    'order_id'   =>$orderId,
                     
                );
                $OrderGoodsModel = new OrderGoodsModel();
                if($OrderGoodsModel->create($goods)){
                    if($OrderGoodsModel->add()){
                        
                    }else {
                        $this->error($OrderGoodsModel->getError());
                    }
                }else{
                    $this->error($OrderGoodsModel->getError());
                }
            }
            $CartModel = new CartModel();
            $CartModel->clearCart();
            $this->success('提交成功',U("Index/index"));
        }else{
            $this->error($OrderModel->getError());
        }
    }
    public function initCart(){
        $CartModel = new CartModel();
        $where['status'] = 1;
        $cartList = $this->listAll($CartModel,$where);
        $GoodsModel = new GoodsModel();
        $total = array();
        foreach ($cartList as $k=>$v){
            $goods = $GoodsModel->info($v['goods_id']);
            $cartList[$k]['pic_url'] = $goods['pic_url'];
            $cartList[$k]['name']   = $goods['name'];
            $cartList[$k]['subtotal']= number_format($goods['price']*$v['number'],2);


            if($goods['goods_type'] == '1'){
                $total['total']+=$cartList[$k]['subtotal'];
            }
            if($goods['goods_type'] == '2'){
               $total['score']+=$cartList[$k]['subtotal'];
            }
           
            $cartList[$k]['price']   = number_format($goods['price'],2);
        }
        $total['total'] = number_format($total['total'],2);
        $this->cartList = $cartList;
        $this->total    = $total;
    }
}

?>