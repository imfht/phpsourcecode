<?php
namespace Admin\Controller;
use Admin\Model\CategoryModel;
use TopSDK\Api\TopApi;
use Admin\Model\CollectGoodsModel;
use Admin\Model\GoodsModel;
class CollectController extends AdminController
{
    
    public function index(){
        $id = I('id',0);
        if(IS_POST){
            $q              =   I('q');
            $cid            =   I('cid');
            $sort           =   I('sort');
            $startTkRate    =   I("start_tk_rate");
            $endTkRate      =   I("end_tk_rate");
            $startPrice     =   I('start_price');
            $endPrice       =   I('end_price');
            $name           =   I('name','');
            $pageNo       =   I('page_no')? I('page_no') : 1;
            $pageSize       =   I('page_size')? I('page_size') : 10;
            $cateId         =   I('cate_id') ? intval(I('cate_id')) : 0;
            $taobao = new TopApi(C('APP_KEY'), C('APP_SECRET'));
            
            $result = $taobao->getItemList($q,$cid,false,$startPrice,$endPrice,$startTkRate,$endTkRate,$sort,$pageNo,$pageSize);
         
            $CollectGoodsModel = new CollectGoodsModel();
            if(is_array($result)){
                foreach ($result as $k => $v){
                    $result[$k]['cate_id'] = $cateId;
                    if($CollectGoodsModel->create($result[$k])){
                        $result[$k]['id'] = $CollectGoodsModel->add();
                    }                   
                }
                D('CollectRule')->create();
                if($id > 0){
                    $where['id'] = $id;
                    D('CollectRule')->where($where)->save();
                }else {
                    D('CollectRule')->add();
                }
                
                $this->assign('_list',$result);
                $this->display('collect');
            }else{
                $this->error($taobao->error());
            }
        }else{
            if ($id > 0){
                $where['id'] = $id;
                $rule = D('CollectRule')->where($where)->find();
                $this->assign('rule',$rule);
            }
            $category = D('CategoryGoods')->getTree();
             
            $this->assign('category', $category);
            $this->display();
        } 
    }
    public function update(){
        $q              =   I('q');
        $cid            =   I('cid');
        $sort           =   I('sort');
        $startTkRate    =   I("start_tk_rate");
        $endTkRate      =   I("end_tk_rate");
        $startPrice     =   I('start_price');
        $endPrice       =   I('end_price');
        $name           =   I('name','');
        $pageSize       =   I('num')? I('num') : 30;
        $cateId         =   I('cate_id') ? intval(I('cate_id')) : 0;      
        $id = I('id',0);
        D('CollectRule')->create();
        if($id > 0){
            $where['id'] = $id;
            if (false!==D('CollectRule')->where($where)->save()){
                $this->success('保存成功');
            }else {
                $this->error('保存失败');
            }
        }else {
            if(false!==D('CollectRule')->add()){
                $this->success('保存成功');
            }else {
                 $this->error('保存失败');
            }
        }
    }
    public function collectList(){
        $CollectGoodsModel = new CollectGoodsModel();
        $list = $this->lists($CollectGoodsModel);
        $this->assign('_list',$list);
        $this->display('collect');
    }
    public function addToGoods(){
         $id = array_unique((array)I('id',0));       
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        $CollectGoodsModel = new CollectGoodsModel();
        $info = $this->listAll('CollectGoods',$where);
        $GoodsModel = new GoodsModel();
		$add = 0;
		$err = 0;
		$exist = 0;
		foreach($info as $row){
		    $collectGoodsId = $row['id'];
		    unset($row['id']);
		    $row['seo_title'] = $row['name'];
			if($res = $GoodsModel->addGoods($row)){
				
				if($res>0){
					$where = array(
						'id' => $collectGoodsId
					);
					$CollectGoodsModel->where($where)->delete();
					$add++;
				}elseif($res == -2){
				    $CollectGoodsModel->where($where)->delete();
				    $exist++;
				}
				else{
					$err++;
				}
            }else {
				$err++;
			}
        }
		if($err > 0){
			$this->error($err.'件商品上架失败，'.$add.'件商品上架成功',U('Collect/collectList'));
        }else {
			$this->success($add.'件商品上架成功,'.$exist.'件重复商品未添加',U('Collect/collectList'));
        }  
    }
    public function updateGoodsInfo(){
        if(IS_POST){
            $this->display();
            $catId = I('cate_id');
            $where = array();
            if(isset($catId)){
                $where['cate_id'] = $catId;
            }
            $GoodsModel = new GoodsModel();
            
            $taobao = new TopApi(C('APP_KEY'), C('APP_SECRET'));
            $err=0;
            $count=0;
            $goodsList = $GoodsModel->field('id,num_iid,name')->where($where)->select();
            if(is_array($goodsList)){
                foreach ($goodsList as $row){
                    if(!empty($row['num_iid'])){
                        $result = $taobao->getItemInfo($row['num_iid']);
                        $where['id'] = $row['id'];
                        if($result){
                            foreach ($result as $r){

                                unset($r['name']);
                                unset($r['pic_url']);
                                $r['status'] = 1;
                               
                                $res=$GoodsModel->where($where)->save($r);
                                if(false!==$res){
                                    show_msg('更新...'.$row['name']);
                                    $count++;
                                }else{
                                    $where['id'] = $row['id'];                                    
                                    show_msg("更新...<a target='_blank' href='".U('Goods/edit',array('id'=>$row['id']))."'>".$row['name']."</a>...".$GoodsModel->getError());
                                    $err++;
                                }
                            }
                            
                        }else{                      

                            show_msg("更新...<a target='_blank' href='".U('Goods/edit',array('id'=>$row['id']))."'>".$row['name']."</a>...未获得该商品信息，可能已下架");
                            $GoodsModel->where($where)->save(array('status'=>3));
                            $err++;
                        }
                    }else{
                        show_msg('更新...'.$row['name'].'...非淘宝天猫商品，跳过');
                    }                                  
                }
                if($err > 0 ){
                    show_msg($err.'件商品更新失败');
                }else {
                    show_msg('全部更新完成');
                }
                show_msg('更新'.$count.'件商品');
            }
            
           
        }else{
            $category = D('CategoryGoods')->getGoodsCategory();
             
            $this->assign('category', $category);
            $this->display();
        }
        
    }
    public function updateMessage(){
        $catId = I('cate_id');
        $where = array();
        if(isset($catId)){
            $where['cate_id'] = $catId;
        }
        $GoodsModel = new GoodsModel();
        $goodsList = $GoodsModel->field('id,num_iid,name')->where($where)->select();
        $this->assign('goods_list',json_encode($goodsList));
        $this->display();
    }
    public function updateOne(){
        $taobao = new TopApi(C('APP_KEY'), C('APP_SECRET'));
        $num_iid                                =   I('num_iid');
        $goodsId                                =   I('id');
        $goodsName                              =   I('name');

        if(!empty($num_iid)){
            $result = $taobao->getItemInfo($num_iid);
            $where['id']                        =   $goodsId;
            $GoodsModel                         =   new GoodsModel();
            if($result){
                $goods                          =   $GoodsModel->info($goodsId);
                foreach ($result as $r){

                    unset($r['name']);
                    unset($r['pic_url']);

                    if(!empty($goods['click_url'])){
                        $tpwd                   =   $taobao->getTpwd($goods['click_url'],$goods['name'],$goods['pic_url']);
                        $r['tpwd']              =   $tpwd;

                    }
                    $r['status']                = 1;

                    $res                        =   $GoodsModel->where($where)->save($r);
                    if(false!==$res){


                        $this->success('更新...'.$goodsName);

                    }else{
                        $where['id']            =   $goodsId;
                        $this->error("更新...<a target='_blank' href='".U('Goods/edit',array('id'=>$goodsId))."'>".$goodsName."</a>...".$GoodsModel->getError());

                    }
                }

            }else{

                $this->error("更新...<a target='_blank' href='".U('Goods/edit',array('id'=>$goodsId))."'>".$goodsName."</a>...未获得该商品信息，可能已下架");
                $GoodsModel->where($where)->save(array('status'=>3));
            }
        }else{
            $this->error('更新...'.$goodsName.'...非淘宝天猫商品，跳过');
        }
    }
    public function collectRule(){
        $rules = $this->lists('CollectRule');
        $this->assign('_list',$rules);
        $this->display();
    }
    public function delCollectGoods(){
        $id = array_unique((array)I('id',0));       
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
        
       
        if(M('CollectGoods')->where($where)->delete()){
            $this->success('操作成功',U('Collect/collectList'));
        }else {
            $this->error('删除失败',U('Collect/collectList'));
        }
    }
    public function delCollectRule(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where['id'] =   array('in',$id);
    
        if(M('CollectRule')->where($where)->delete()){
            $this->success('操作成功');
        }else {
            $this->error('删除失败');
        }
    }
    
}

?>