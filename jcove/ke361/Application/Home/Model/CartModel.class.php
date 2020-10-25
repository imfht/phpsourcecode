<?php
namespace Home\Model;

use Think\Model;
class CartModel extends Model
{
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
    );
    public function addToCart($id){
        $GoodsModel = new GoodsModel();
        $goods = $GoodsModel->info($id);
        if($goods){
            
            $cartGoods = $this->getCartGoodsById($id);
            $goodsNumber = I('number',1,'intval');
            if($cartGoods){
                $where['id'] = $cartGoods['id'];
                $data['number'] = $cartGoods['number']+$goodsNumber;
                $this->create($data);
                return $this->where($where)->save();
            }else {
                $data['goods_id'] = $goods['id'];
                $data['goods_type'] = $goods['goods_type'];
                $data['uid']  = UID;
                $this->create($data);
                return $this->add();
            }   
         }else{
             $this->error.='无效的商品';
         }
         return false;
    }
    public function getCartGoodsById($goodsId){
        if(intval($goodsId) > 0 ){
            $where['goods_id'] = $goodsId;
            $where['uid']  = UID;
            return $this->where($where)->find();
        }else {
            return 0;
        }
    }
    public function clearCart($all = FALSE){
        $where['uid'] = UID;
        if(!$all){
            $where['status'] = 1;
        }
        $this->where($where)->delete();      
    }
    public function total(){
        $where['uid'] = UID;
        $where['status']=1;
        $cartList = $this->field('goods_id,number')->where($where)->select();
        $GoodsModel = new GoodsModel();
        $total = array(
            'score'=>0,
            'total'=>0
        );
        foreach ($cartList as $k=>$v){
            $goods = $GoodsModel->info($v['goods_id']);

            $cartList[$k]['name']   = $goods['name'];

            $cartList[$k]['subtotal']= $goods['price']*$v['number'];
            if($goods['goods_type'] == '1'){
                $cartList[$k]['subtotal_format'] = number_format($cartList[$k]['subtotal'],2);
                $cartList[$k]['price']   = $goods['price'];
                $total['total']+=$cartList[$k]['subtotal'];
            }
            if($goods['goods_type'] == '2'){
                $cartList[$k]['subtotal_format'] = $cartList[$k]['subtotal'].'积分';
                $cartList[$k]['price']   = number_format($goods['price']).'积分';
                $total['score'] += $cartList[$k]['subtotal'];
            }
        }
        $total['total']=number_format( $total['total'],2);
        return $total;
    }
    
}

?>