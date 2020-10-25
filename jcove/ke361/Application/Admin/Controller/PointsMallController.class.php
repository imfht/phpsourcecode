<?php
namespace Admin\Controller;

class PointsMallController extends AdminController
{
    public function index(){
       
       
        $where = array('goods_type'=>2);       
        $goodsList = $this->lists('Goods',$where,'sort desc , id desc',array());
        $this->assign('goodsList', $goodsList);
        $this->assign('category',D('CategoryGoods')->getGoodsCategory(1));
        $this->assign('cate_id',$cateId);
        $this->display();
    }
    public function edit(){
    
        $goodsId = I('id');
        if(IS_POST){
            $goods['name'] = I('name');
            $goods['cate_id'] = I('cate_id');
            $goods['tid'] = I('tid');
            $goods['nick'] = I('nick');
            $goods['seo_title'] = I('seo_title');
            $goods['seo_keywords'] = I('seo_keywords');
            $goods['seo_description'] = I('seo_description');
            $goods['goods_type'] = 2;
            $goods['price'] = I('post.price', 0.00, 'floatval');
            $goods['discount_price'] = I('post.discount_price', 0.00, 'floatval');
            $goods['click_url'] = I('click_url');
             
            $goods['status'] = I('status');
            $goods['item_body'] = I('item_body');
            $goods['pic_url'] = I('pic_url');
    
            if (!$goods['name'])
                $this->error('请填写商品名称');
            if (!$goods['price'])
                $this->error('请填写商品价格');
    
            if (empty($goodsId) && empty($goods['pic_url'])) {
                $this->error('请上传商品图片');
            }
    
             
    
            $GoodsModel = D('goods');
            $GoodsModel->create();
            if (empty($goodsId)) {
                $GoodsModel->add();
            } else {
                $GoodsModel->where("id='{$goodsId}'")->save();
            }
    
            $this->success('操作成功',U('Goods/index'));
        }
        $this->assign('goods', D('Goods')->info($goodsId));
         
        $this->display();
    }
    public function del(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('Goods')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
}

?>