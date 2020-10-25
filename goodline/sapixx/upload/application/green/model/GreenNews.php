<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 内容管理
 */
namespace app\green\model;
use think\Model;

class GreenNews extends Model{
    
    protected $autoWriteTimestamp = true;
    protected $createTime = false;


    //添加或编辑
    public static function edit($member_miniapp_id,$param){
        $data['types']       = trim($param['types']);
        $data['cate_id']     = trim($param['cate_id']);
        $data['desc']        = trim($param['desc']);
        $data['img']         = trim($param['img']);
        $data['title']       = trim($param['title']);
        $data['cate_name']   = trim($param['cate_name']);
        $data['content']     = trim($param['content']);
        $data['update_time'] = time();
        if(isset($param['id'])){
            $condition['id']                = $param['id'];
            $condition['member_miniapp_id'] = $member_miniapp_id;
            return self::where($condition)->update($data);
        }else{
            $data['member_miniapp_id'] = $member_miniapp_id;
            return self::create($data);
        }
    } 
}