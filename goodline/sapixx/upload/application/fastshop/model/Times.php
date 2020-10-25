<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 抢购时间管理
 */
namespace app\fastshop\model;
use think\Model;

class Times extends Model{

    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_times';

    //添加或编辑
    public function edit($param){
        $data['name']       = trim($param['name']);
        $data['sort']       = trim($param['sort']);
        $data['start_time'] = $param['start_time'];
        $data['end_time']   = $param['end_time'];
        if(isset($param['id'])){
            return self::update($data,['id'=>(int)$param['id']]);
        }else{
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::insert($data);
        }
    }
}