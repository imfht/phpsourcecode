<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 内容管理
 */
namespace app\popupshop\model;
use think\Model;

class Article extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_article';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    //添加或编辑
    public function edit($member_miniapp_id,$param){
        $data['types']       = trim($param['types']);
        $data['title']       = trim($param['title']);
        $data['content']     = trim($param['content']);
        $data['update_time'] = time();
        if(isset($param['id'])){
            $condition['id']                = $param['id'];
            $condition['member_miniapp_id'] = $member_miniapp_id;
            return self::save($data,$condition);
        }else{
            $data['member_miniapp_id'] = $member_miniapp_id;
            return self::insert($data);
        }
    } 
}