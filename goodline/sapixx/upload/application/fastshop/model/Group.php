<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 团购管理
 */
namespace app\fastshop\model;
use think\Model;
use filter\Filter;

class Group extends Model{
    
    protected $pk    = 'id';
    protected $table  = 'ai_fastshop_group';

    public function item(){
        return $this->hasOne('Item','id','item_id');
    }
    
    /**
     * 读取商品列表
     * @param string $status
     * @param string $keyword
     * @return void
     */
    public function lists(int $member_id){     
        return self::where(['member_miniapp_id' => $member_id])->order('id desc')->paginate(10);
    }

    /**
     * 读取需要选择商品列表
     *
     * @param integer $sid 专题ID
     * @param integer $cid 栏目ID
     * @param string  $keyword 搜索关键字
     * @return void
     */
    public function itemList(int $member_id,int $cid,string $keyword = null){
        $where   = [];
        $where[] = ['member_miniapp_id','=',$member_id];
        $where[] = ['is_sale','<>',1];
        if ($cid) {
            $where[] = ['','exp', self::raw("FIND_IN_SET({$cid},category_path_id)")];
        }
        if(!empty($keyword)){
            $keyword = Filter::filter_escape($keyword);
            $where[] = ["name","like","%{$keyword}%"];
        }
        return model('Item')->alias('A')->where($where)->whereNotExists(function ($query){
            $query->table('ai_fastshop_group')->alias('B')->WHERE("A.id","=","B.item_id");
        })->order('sort desc,id desc')->paginate(20,false,['query'=>['cid' => $cid,'keyword' => $keyword]]);

    }

    /**
     * 批量选择操作
     * @param int $sid     专题ID
     * @param string $ids  要批量操作的ids
     */
    public function ids_action(int $member_miniapp_id,string $ids){ 
        $ids = ids($ids,true);
        $data = [];
        foreach ($ids as $key => $id) {
            $data[$key]['member_miniapp_id'] = $member_miniapp_id;
            $data[$key]['item_id']     = $id;
            $data[$key]['amount']      = 0;
            $data[$key]['hao_people']  = 10;
            $data[$key]['uids']        = json_encode([]);
        }
        if(!empty($data)){
            return self::insertAll($data);
        }
        return false;
    }
}