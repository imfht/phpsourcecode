<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 广告管理
 */
namespace app\fastshop\model;
use think\Model;

class Banner extends Model{
      
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_banner';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    
    //添加或编辑
    public function edit($param){
        $data['group_id']    = trim($param['group_id']);
        $data['open_type']   = trim($param['open_type']);
        $data['title']       = trim($param['title']);
        $data['link']        = trim($param['link']);
        $data['picture']     = trim($param['picture']);
        $data['update_time'] = time();
        if(isset($param['id'])){
            $condition['id'] = $param['id'];
            $condition['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::save($data,$condition);
        }else{
            $data['create_time']         = time();
            $data['member_miniapp_id']   = $param['member_miniapp_id'];
            return self::insert($data);
        }
    } 
}