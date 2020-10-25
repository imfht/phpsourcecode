<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 信息栏目
 */
namespace app\green\model;
use think\Model;
use category\Tree;

class GreenNewsCate extends Model{


    //添加或编辑
    public static function edit($param){
        $data['title']     = trim($param['title']);
        $data['name']      = trim($param['name']);
        $data['sort']      = trim($param['sort']);
        $data['picture']   = trim($param['picture']);
        $data['update_time']  = time();
        if(isset($param['id'])){
            return self::update($data,['id'=>(int)$param['id']]);
        }else{
            $data['create_time']       = time();
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::create($data);
        }
    } 
}