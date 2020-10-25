<?php
namespace Home\Controller;

use Home\Controller\HomeController;
use Home\Model\GoodsModel;

class PointsMallController extends HomeController
{
    public function index(){
   
        $where['status'] = 1;
        $where['goods_type'] = 2;
        $GoodsModel = new GoodsModel();
        $sort = I('sort','create_time');
        $type= I('type','desc');   
         
        $list = $this->lists($GoodsModel,$where,array($sort=>$type));
        $type = ($type=='desc') ? 'asc':'desc';
        $this->assign('goods',$list);
        $this->assign('cate',$cate);
        $this->assign('sort',$sort);
        $this->assign('type',$type);
        if(IS_AJAX){
            $result['p']=I('get.p')+1;
            $result['content']=$this->fetch('ajaxgoodslist');
            $result['errno']=0;
            $this->ajaxReturn($result);
        }
        $this->setSiteTitle('积分商城');
        $this->setKeyWords('积分商城');
        $this->setDescription('积分商城');
   
        $this->display();
    }
    public function info(){
        $id = I('get.id','','intval');
        $where['id'] = $id;
        $where['goods_type'] = 2;
        $goods = D('Goods')->where($where)->find();
        if($goods){
           
            $this->assign('goods',$goods);
            if(!empty($goods['seo_title'])){
                $this->setSiteTitle($goods['seo_title']);
            }else {
                $this->setSiteTitle($goods['name']);
            }
            $this->display();
        }else{
            $this->error('商品不存在');
        }
    }
}

?>