<?php
namespace Admin\Controller;

use Think\Controller;
use Admin\Model\TopicModel;
use Admin\Model\GoodsModel;

class TopicController extends AdminController
{
    public function index(){
        $this->assign('_list',$this->lists(D('Topic')));
        $this->display();
        
    }   
    public function edit(){
        $id = I('id');
        $TopicModel = new TopicModel();
        if(IS_POST){
            if(is_numeric($id) && $id >0 ){
                $where['id'] = $id;
                $TopicModel->create();
                if($TopicModel->where($where)->save()){
                    $this->success('操作成功');
                }else {
                       
                    $this->error('操作失败');
                }
            }else{
                $TopicModel->create();
                if($TopicModel->add()){
                    $this->success('添加成功');
                }else {
                    $this->success('添加失败');
                }
            }
        }else{
            if(isset($id)){
                $where['id'] = $id;
                $where['status'] = 1;
                $this->assign('topic',D('Topic')->where($where)->find());
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
        if(M('Topic')->where($where)->delete()){
            $this->success('删除成功',U('index'));
        }else {
            $this->error('删除过程中遇到错误');
        }
        
    }
    public function topicGoods($id =0){
        if($id==0){
            $this->error('无效的专题id');
        }
        $where['tid'] = $id;
        $GoodsModel = new GoodsModel();
        $topicGoods = $GoodsModel->where($where)->select();
        
        $map['id'] = $id;
        $TopicModel = new TopicModel();
        $topic = $TopicModel->where($map)->find();
        $this->assign('topic',$topic);
        $this->assign('_list',$topicGoods);
        $this->display();
    }
    public function delGoods(){
        $id = I('get.id',0,'intval');
        $goodsId = I('get.goods_id',0,'intval');
        if($id == 0){
            $this->error('无效的专题id');
        }
        if($goodsId == 0){
            $this->error('无效的商品id');
        }
        $where['id'] =$goodsId;
        $GoodsModel = new GoodsModel();
        $data['tid'] = 0; 
        if($GoodsModel->where($where)->save($data)){
            $this->success('删除成功');
        }else {
            $this->error('删除过程遇到问题');
        }
        
    }
}

?>