<?php
namespace Admin\Controller;

use Admin\Controller\AdminController;
use Admin\Model\GoodsModel;
use TopSDK\Api\TopApi;
use Admin\Model\DocumentModel;

class GoodsController extends AdminController
{
    public function index(){
       $cateId = I('cate_id',0);
       $recommend = I('recommend',0);
       $new       = I('new',0);
       $name      = I('name','');  
       $where['goods_type'] < 2;
       $status = I('status','');
       if($cateId>0){
           $where['cate_id'] = $cateId;
       }
       if($recommend > 0){
           $where['recommend'] = $recommend;
       }
       if($new > 0){
           $where['new'] = $new;
       }
       if(!empty($name)){
           $where['name'] = array('like','%'.$name.'%');
       }
       if(!empty($status)){
           if($status=='offsale'){
               $where['status'] = 0;
           }
           if($status=='invalid'){
               $where['status'] = 3;
           }
           
       }
       
        $goodsList = $this->lists('Goods',$where,'sort desc , id desc',array());
        $this->assign('goodsList', $goodsList);
        $this->assign('category',D('CategoryGoods')->getTree());
        $this->assign('cate_id',$cateId);
        $this->display();
    }
    public function editField(){
        if(IS_AJAX){
            $field =I('field','');
            $id    =I('id',0);
            $value =I('value','');
            if(empty($field)||empty($value)){
                $this->error('数据不能为空');
            }
            if($id > 0 ){
                $where['id'] = $id;
                $data[$field]= $value;
                if(false!==D('Goods')->where($where)->save($data)){
                    $this->success('操作成功');
                }else {
                    $this->error('操作失败');
                }
            }else {
                $this->error('id无效');
            }
        }
    }
    public function edit(){
      
		$goodsId = I('id');
        if(IS_POST){
            $goods['name']                      = I('name');
            $goods['num_iid']                   = I('num_iid');
            $goods['cate_id']                   = I('cate_id');
            $goods['tid']                       = I('tid');
            $goods['nick']                      = I('nick');
            $goods['seo_title']                 = I('seo_title');
            $goods['seo_keywords']              = I('seo_keywords');
            $goods['seo_description']           = I('seo_description');
            $goods['goods_type']                = I('goods_type');
            $goods['price']                     = I('post.price', 0.00, 'floatval');
            $goods['market_price']              = I('post.market_price', 0.00, 'floatval');
            $goods['click_url']                 = I('click_url');
            $goods['item_url']                  = I('item_url');
            $goods['description']               = I('description');
            $goods['item_body']                 = I('item_body');
            $goods['pic_url']                   = I('pic_url');
            $goods['volume']                    = I('volume');
            $goods['tpwd']                      = I('tpwd');
            if(empty($goods['seo_title'])){
                $goods['seo_title'] = $goods['name'];
            }
            if(empty($goods['seo_keywords'])){
                hook('getKeyWords',$goods['seo_title']);
                $goods['seo_keywords'] = session('get_keywords');
                session('get_keywords',null);
            }
            if (!$goods['name'])
                $this->error('请填写商品名称');
            if (!$goods['price'])
                $this->error('请填写商品价格');
        
            if (empty($goodsId) && empty($goods['pic_url'])) {
                $this->error('请上传商品图片');
            }
        
           
        
            $GoodsModel = D('goods');
            $GoodsModel->create($goods);
            if (empty($goodsId)) {
                $GoodsModel->add();
            } else {
                $GoodsModel->where("id='{$goodsId}'")->save();
            }
            session('last_categroy',$goods['cate_id']);
          
            $this->success('操作成功',U('Goods/index'));
        }
       
        $where['status'] = 1; 
        $category = D('CategoryGoods')->getTree(0,$where);
     
        $this->assign('category', $category);
        $this->assign('topic', $this->listAll(D('Topic')));
        $this->assign('tags',$this->listAll(D('Tag')));
        $this->assign('goods', D('Goods')->info($goodsId));
   
        $this->display();
    }
    public function status($status = 0){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
      
        $GoodsModel = new GoodsModel();
         $GoodsModel->where($where)->save(array('status'=>$status ? 0 : 1));
     
        $this->success('操作成功',U('Goods/index'));
    }
    public function recommend($recommend = 0){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        $GoodsModel = new GoodsModel();
        $GoodsModel->where($where)->save(array('recommend'=>$recommend ? 0 : 1));
        $this->success('操作成功',U('Goods/index'));
    }
    public function news($new = 0){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        $GoodsModel = new GoodsModel();
        $GoodsModel->where($where)->save(array('new'=>$new ? 0 : 1));
        $this->success('操作成功',U('Goods/index'));
    }
    /**
     * 移动商品
     * @author huajie <banhuajie@163.com>
     */
    public function move() {
        if(empty($_POST['id'])) {
            $this->error('请选择要移动的商品！');
        }
        session('moveGoods', $_POST['id']);
        session('copyGoods', null);
        $this->success('剪切成功！请选择要粘贴的分类，然后粘贴');
    }
    /**
     * 拷贝商品
     * @author huajie <banhuajie@163.com>
     */
    public function copy() {
        if(empty($_POST['id'])) {
            $this->error('请选择要复制的商品！');
        }
        session('copyGoods', $_POST['id']);
        session('moveGoods', null);
        $this->success('复制成功！请选择要粘贴的分类，然后粘贴');
    }
    /**
     * 粘贴商品
     * @author huajie <banhuajie@163.com>
     */
    public function paste() {
        $moveList = session('moveGoods');
        $copyList = session('copyGoods');
        if(empty($moveList) && empty($copyList)) {
            $this->error('没有选择商品！');
        }
        if(!isset($_POST['cate_id'])) {
            $this->error('请选择要粘贴到的分类！');
        }
        $cate_id = I('post.cate_id');   //当前分类

        if(!empty($moveList)) {// 移动    TODO:检查name重复
            foreach ($moveList as $key=>$value){
                $Model              =   M('Goods');
                $map['id']          =   $value;
                $data['cate_id']=   $cate_id;

                $res = $Model->where($map)->save($data);
            }
            session('moveGoods', null);
            if(false !== $res){
                $this->success('商品移动成功！');
            }else{
                $this->error('商品移动失败！');
            }
        }elseif(!empty($copyList)){ // 复制
            foreach ($copyList as $key=>$value){
                $Model  =   M('Goods');
                $data   =   $Model->find($value);
                unset($data['id']);
                $data['c_time']    =   NOW_TIME;
                $result   =  $Model->add($data);
              
            }
            session('copyGoods', null);
            if($res){
                $this->success('商品复制成功！');
            }else{
                $this->error('商品复制失败！');
            }
        }
    }
    
    public function relation(){
        $list = $this->lists('GoodsRelation');
        $GoodsM = new GoodsModel();
        $DocumentM = new DocumentModel();
        foreach ($list as $k=>$v){
            $goods = $GoodsM->info($v['goods_id']);
            $list[$k]['goods_name'] = !empty($goods['name']) ? $goods['name']:'无效，可能已删除';
            $document = $DocumentM->detail($v['object_id']);
            $list[$k]['object_name'] = !empty($document['title']) ? $document['title']:'无效，可能已删除';
        }
        $this->assign('_list',$list);
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
    public function delRelation(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        if(M('GoodsRelation')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
    public function  getItemInfo($url){
        preg_match('/taobao.com/', $url,$t);
        preg_match('/tmall.com/', $url,$tm);
        if(isset($t['0']) || isset($tm['0'])){
            preg_match('/id=\d*/', $url,$data);
            $numIid = trim($data[0],'id=');
            $taobao = new TopApi(C('APP_KEY'), C('APP_SECRET'));
            $item = $taobao->getItemInfo($numIid);

            $result = array(
                'errno' =>0,
                'obj'   =>array()
            );
            if(is_array($item)){
                $result['obj']=$item[0];
            }else{
                $result['errno']=$taobao->error();
            }
            $this->ajaxReturn($result);
        }
        
    }
    public function searchGoods($k){

        $result = array(
            'errno' => 0,
            'content' => '',
            'message' => ''
        );
        if(empty($k)){
            $result['errno'] = 1;
            $result['message'] ='关键词不能为空';
        }else {
           $where['name'] = array('like',"%".$k."%");
           $GoodsModel = new GoodsModel();
           $goodsList = $this->listAll($GoodsModel,$where);
           if(empty($goodsList)){
               $result['errno'] = 2;
               $result['message'] ='未找到相关商品';
           }else {
               $result['content'] = $goodsList;
           }
        }
        $this->ajaxReturn($result);
    }
    public function ajaxGoodsInfo($id){
        $GoodsModel = new GoodsModel();
        $goods = $GoodsModel->info($id);
        $result = array(
            'errno' =>0,
            'content' =>'',
            'message' => ''
        );
        if(!empty($goods)){
            $goods['url'] = U('Goods/info',array('id'=>$goods['id']));
            $result['content'] = $goods;
        }else {
            $result['errno'] = 1;
            $result['message'] = '无效的商品';
        }
        $this->ajaxReturn($result);
    }

    public function getTpwd(){
        $url                                        =   I('url');
        $title                                      =   I('title');
        $image                                      =   I('image');
        $image                                      =   get_image_url($image);
        $top                                        =   new TopApi(C('APP_KEY'), C('APP_SECRET'));
        $pwd                                        =   $top->getTpwd($url,$title,$image);
        $result = array(
            'errno' =>0,
            'content' =>'',
            'message' => ''
        );
        if($pwd){
            $result['content']                      =   $pwd;
        }else{
            $result['errno']                        =   2;
            $result['message']                      =   $top->error();
        }
        $this->ajaxReturn($result);
    }
  
    
}

?>