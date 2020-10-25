<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城管理
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Item;
use app\popupshop\model\Category;
use think\Db;
use think\facade\Request;

class Shop extends Base{
   

    /**
     * 全部分类布局
     * @param integer $cate_id 读取ID
     * @return json
     */
    public function cate(){
        $root_cate = Category::where(['member_miniapp_id' => $this->miniapp_id,'parent_id' => 0])->field('id,parent_id,title,name,picture')->order('sort desc,id desc')->select()->toArray();       
        if (empty($root_cate)) {
            return enjson(403,'读取目录失败');
        }else{
            $data['root_data'] = $root_cate;
            $ids = array_column($root_cate,'id');
            $data['subs_data'] = Category::whereIn('parent_id',$ids)->order('sort desc,id desc')->select();
            return json(['code'=>200,'msg'=>'成功','data' => $data]);
        }
    }

    /**
     * 读取首页分类
     * @return json
     */
    public function cateTop(){
        $data['types']   = Request::param('types',0);
        $data['cate_id'] = Request::param('cate_id',0);
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            if($data['types']){
                $condition[] = ['types','=',1];
            }else{
                $condition[] = ['parent_id','=',$data['cate_id']];
            }
            $data = Category::where($condition)->field("id,parent_id,title,name,picture")->order('sort desc,id desc')->select()->toArray();
            if (empty($data)) {
                return enjson(204,'分类不存在');
            }
            return enjson(200,'成功',$data);
        }
        return enjson($rel['code'],'签名验证失败');
    }  
    
    /**
     * 读取某个分类下的商品列表
     * @param integer api 读取ID
     * @return json
     */
    public function cateItem(){
        $data['cate_id'] = Request::param('cate_id',0);
        $data['page']    = Request::param('page',0);
        $data['sign']    = Request::param('sign');
        $data['types']   = Request::param('types',0);
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['is_sale','=',2];
            if($data['cate_id']){
                $condition[] = ['','exp',Db::raw("FIND_IN_SET({$data['cate_id']},category_path_id)")];
            }else{
                $condition[] = ['types','=',$data['types']];
            }
            $result = Item::where($condition)->field('id,category_id,name,sell_price,market_price,note,img')->order('sort desc,id desc')->paginate(20,true)->toArray();
            if(empty($result['data'])){
                return enjson(204,'没有内容了');
            }
            return enjson(200,'成功',$result['data']);
        }
        return enjson($rel['code'],'签名验证失败');
    }

    /**
     * 搜索商品ID
     * @param integer api 读取ID
     * @return json
     */
    public function search(){
        $data['keyword'] = Request::param('keyword','');
        $data['page']    = Request::param('page',0);
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $keyword = Request::param('keyword');
            if(empty($keyword)){
                return enjson(204,'未输入关键字');
            }
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['is_sale','=',2];
            $result = Item::where($condition)->withSearch(['name'],['name' => $keyword])->field('id,category_id,name,sell_price,market_price,img')->order('sort desc,id desc')->paginate(10,true);
            $data = [];
            foreach ($result as $key => $value) {
                $data[$key]        = $value;
                $data[$key]['img'] = $value['img'].'?x-oss-process = style/300';
            }
            return enjson(200,'成功',$data);
        }
        return enjson(204,'未输入关键字');
    }

    
    /**
     * 读取单个商品信息
     * @param integer $id 商品ID
     * @return void
     */
    public function item(int $id){
        $param['id']   = Request::param('id',0);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] == 200){
            //查找商品SPU
            $item = Item::where(['is_sale'=>2,'id' => $id,'member_miniapp_id' => $this->miniapp_id])
                    ->field('id,category_id,name,note,sell_price,market_price,points,repoints,weight,img,imgs,content')
                    ->find();
            if(empty($item)){
                return json(['code'=>403,'msg'=>'没有内容']);
            }
            $data = [];
            $data['content']      = $item->content;
            $data['name']         = $item->name;
            $data['note']         = $item->note;
            $data['market_price'] = $item->market_price;
            $data['sell_price']   = $item->sell_price;
            $data['points']       = $item->points;
            $data['repoints']     = $item->repoints;
            $data['img']          = $item->img.'?x-oss-process = style/500';
            $data['imgs']         = json_decode($item->imgs,true);
            return enjson(200,'成功',$data);
        }
        return enjson(204,'签名错误');
    } 
}