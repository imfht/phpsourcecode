<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 广告位管理
 */
namespace app\green\model;
use think\Model;

class GreenAdwords extends Model{

    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    //联盟城市
    public function operate(){
        return $this->hasOne('operate','id','operate_id');
    }

    //添加或编辑
    public function edit($param){
        $data['group']       = trim($param['group']);
        $data['title']       = trim($param['title']);
        $data['link']        = trim($param['link']);
        $data['picture']     = trim($param['picture']);
        $data['open_type']   = trim($param['open_type']);
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