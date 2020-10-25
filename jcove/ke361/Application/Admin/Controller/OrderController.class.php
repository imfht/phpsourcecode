<?php
namespace Admin\Controller;

use Admin\Model\OrderModel;
class OrderController extends AdminController
{
    public function index(){
       
        $status = I('get.status',1);
        if(null!==$status){
            if($status <999){
                $where['status'] = intval($status);
            }      
        }
        $orderList = $this->lists('Order',$where);
       
        int_to_string($orderList,array('status'=>array(0=>'未付款',1=>'未发货',2=>'已发货',3=>'已完成',-1=>'已删除'))) ;
        
         
        $this->assign('_list',$orderList);
        $this->display();
    }
    public function detail($id){
        empty($id) && $this->error('参数不能为空！');
        $where['id'] = $id;
        $order = D('Order')->where($where)->find();
      
        if($order){
            
            $map['order_id'] = $id;
            $goodsList = D('OrderGoods')->where($map)->select();
            foreach ($goodsList as $k=>$v){
                $goodsList[$k]['subtotal'] = $v['number']*$v['price'];
            }
            int_to_string($goodsList,array('goods_type'=>array(0=>'推广商品',1=>'自营',2=>'积分商城')));
            
            $order['goods_list'] = $goodsList;
        }
       
        $this->assign('order',$order);
        $this->display();
    }
    public function deliver(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $OrderM = new OrderModel();
        if($OrderM->canDeliver($id)){
            if($OrderM->deliver($id)!==false){
                $this->success('发货成功');
            }else {
                $this->error($OrderM->getError());
            }
            
        }else {
            $this->error($OrderM->getError());
        }
    }
    public function setPaid(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $OrderM = new OrderModel();
        if($OrderM->canPaid($id)){
            if(D('Order')->changeField($id,'status',1)!==false){
                $this->success('付款成功');
            }else {
                $this->error('付款失败');
            }
        }else{
            $this->error($OrderM->getError());
        }
       
    }
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
      
        $this->delete('Order',$where);
    }
}

?>