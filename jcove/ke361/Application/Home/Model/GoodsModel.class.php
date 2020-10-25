<?php
namespace Home\Model;
use Think\Model;
class GoodsModel extends Model{
	
    public function delGoods($id){
        $this->where("id='{$id}'")->delete();
    }


    public function goodsCount($where=1){
        return $this->where($where)->count();            
    }
    
    public function getGoodsList($Page,$where){
        $goods = $this->where($where)->order('`sort` DESC,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($goods as $k => $v){
            $goods[$k]['pic_url'] =get_image_url($v['pic_url']);
        }
        return $goods;
    }
    public function info($id){
        $where = array(
            'id' => $id
        );
        $res = $this->where($where)->find();
       
        $res['pic_url'] = get_image_url($res['pic_url']);
  
        return $res;
    }
    public function hotGoods($cateId,$limit = 10 ){
        $where = array(
            'cate_id' => $cateId
        );
        $res =  $this->where($where)->order('hits DESC,id DESC')->limit($limit)->select();
        foreach ($res as $k => $v){
            $res[$k]['pic_url'] = get_image_url($res[$k]['pic_url']);
        }
       
        
        return $res;
    }
    public function recommendGoods($limit = 4 ){
        $where = array(
            'recommend' => 1
        );
        $cateId = I('cate_id',0);
        if($cateId > 0){
            $where['cate_id'] = $cateId;
        }
        $res =  $this->where($where)->limit($limit)->select();
        foreach ($res as $k=>$v){  
            $res[$k]['url'] = U('/goods/'.$v['id']);
        }
        return $res;
    }
}