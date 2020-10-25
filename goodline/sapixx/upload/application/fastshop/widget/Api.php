<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品前台API扩展控制器
 **/
namespace app\fastshop\widget;
use category\Tree;

class Api{

    /**
     * 读取栏目API
     */
    public function cateFind(int $miniapp_id,int $id = 0){
        return model('Category')->where(['member_miniapp_id' => $miniapp_id])->where('id',$id)->field('id,parent_id,name,picture,sort')->find();
    }

    /**
     * 读取全部分类
     */
    public function cateSelectAll(int $miniapp_id){
        return model('Category')->where(['member_miniapp_id' => $miniapp_id])->cache(true)->field('id,parent_id,name,picture')->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * 读取子栏目
     * 
     */
    public function cateSubClass(int $miniapp_id,int $id = 0){
        $result = self::cateSelectAll($miniapp_id);
        $tree =  new Tree(array('id','parent_id','name','title'));
        return $tree->getChild($id,$result);
    }

    /**
     * 读取目录树
     * 
     */
    public function cateSubTree(int $miniapp_id,int $id = 0){
        $result = self::cateSelectAll($miniapp_id);
        $tree =  new Tree(array('id','parent_id','name','title'));
        return $tree->getTree($result,$id);
    }


    /**
     * ####################################################
     * 读取当前商品
     * @param integer $id   商品ID
     * @return void
     */
    public function itemFind(int $miniapp_id,int $item_id = 0){
        //查找商品SPU
        $item_spu = model('Item')->cache(true)->where(['is_sale'=>2,'id' =>$item_id,'member_miniapp_id' => $miniapp_id])->field('id,is_shopping,category_id,name,sell_price,market_price,weight,points,repoints,img,imgs,content')->find();
        if(empty($item_spu)){
            return false;
        }
        $item_spu['content']      = $item_spu['content'];
        $item_spu['market_price'] = money($item_spu['market_price']);
        $item_spu['sell_price']   = money($item_spu['sell_price']);
        $item_spu['img']          = $item_spu['img'].'?x-oss-process=style/500';
        $item_spu['imgs']         = json_decode($item_spu['imgs'],true);
        $date['url']     = url('pages/index/item',['id' => $item_id]);
        $date['item']    = $item_spu;
        $date['imgs']    = $item_spu['imgs'];
        return $date;  
    }

    /**
     * 最新最新商品
     * @param integer $id  栏目ID
     * @param integer $n   每页数量
     * @return void
     */
    public function itemNew(int $miniapp_id,int $num = 10){
        $result = model('Item')->where(['is_sale'=>2,'member_miniapp_id' => $miniapp_id])
                ->field('id,category_id,name,sell_price,market_price,img')
                ->order('sort desc,id desc')->paginate($num,true)->toArray();
        if(empty($result)){
            return [];
        }
        return $result['data'];
    }

    /**
     * 读取当前栏目下商品
     * @param integer $id  栏目ID
     * @param integer $n   每页数量
     * @return void
     */
    public function itemSelectList(int $cate_id,int $miniapp_id,int $num = 10){
        $result = model('Item')->where(['is_sale'=>2,'category_id' => $cate_id,'member_miniapp_id' => $miniapp_id])
                ->field('id,category_id,name,sell_price,market_price,img')
                ->order('sort desc,id desc')->paginate($num,true)->toArray();
        if(empty($result)){
            return [];
        }
        return $result['data'];
    }

    /**
     * 读取当前栏目下无限极栏目商品
     * @param integer $id  栏目ID
     * @param integer $n   每页数量
     * @return void
     */
    public function itemSelectAll(int $cate_id,int $miniapp_id,int $num = 10){
        $result = model('Item')->where(['is_sale' => 2,'member_miniapp_id' => $miniapp_id])->whereRaw("FIND_IN_SET({$cate_id},category_path_id)")
                ->field('id,category_id,name,sell_price,market_price,img')
                ->cache(true)
                ->order('sort desc,id desc')->paginate($num,true)->toArray();
        if(empty($result)){
            return [];
        }
        return $result['data'];
    }

    /**
     * 搜索商品
     * @param string $keyword 搜索关键字
     * @param string $ida 要搜索的关键词所在商品目录ID
     * @param integer $n  每页数量
     * @return void
     */
    public function itemSearch(string $keyword,int $miniapp_id){
        $result = model('Item')->where(['is_sale'=>2,'member_miniapp_id' => $miniapp_id])->whereLike('name',"%$keyword%")->field('id,category_id,name,sell_price,market_price,img')
                ->order('sort desc,id desc')->paginate(10,true)->toArray();
        return $result['data'];
    }

    /**
     * 读取特定条件的商品
     * @param string $keyword 搜索关键字
     * @param string $ida 要搜索的关键词所在商品目录ID
     * @param integer $n  每页数量
     * @return void
     */
    public function itemTop(int $types,int $miniapp_id,int $num = 10){
        $result = model('Item')->where(['is_sale'=>2,'types'=>$types,'member_miniapp_id' => $miniapp_id])->field('id,category_id,name,sell_price,market_price,img')
                ->order('sort desc,id desc')->paginate($num,true)->toArray();
        return $result['data'];
    }

    /**
     * ###################################################
     * 格式化产品链接和钱的输出样式
     * @param array $data
     * @return void
     */
    public function formartItem(array $param){
        $data = [];
        foreach ($param as $key => $value) {
            $data[$key]                 = $value;
            $data[$key]['market_price'] = money($value['market_price']);
            $data[$key]['sell_price']   = money($value['sell_price']);
            $data[$key]['url']          = url('pages/item/index',['id' => $value['id']]);
            $data[$key]['img']          = $value['img'].'?x-oss-process=style/300';
            $data[$key]['tag_ids']      = empty($value['tag_ids']) ? [] : explode(',',$value['tag_ids']);
        }
        return $data;
    }

    /**
     * 分类接口数据处理
     * @param array $cate
     * @return array
     */
    public function wechatCate(array $cate){
        $data = [];
        if (!empty($cate)) {
            foreach ($cate as $key => $value) {
                $data[$key]['cate_id']   = $value['id'];
                $data[$key]['parent_id'] = $value['parent_id'];
                $data[$key]['title']     = $value['title'];
                $data[$key]['name']      = $value['name'];
                $data[$key]['picture']   = $value['picture'];
            }
        }
        return $data;
    }
}
