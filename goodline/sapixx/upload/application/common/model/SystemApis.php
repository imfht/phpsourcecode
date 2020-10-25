<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 所有第三方接口配置表
 */

namespace app\common\model;
use think\Model;

class SystemApis extends Model{

    protected $pk = 'id';

    /**
     * 读取接口信息
     */
    public static function Config($name){
        $info = self::where(['name' => $name])->find();
        if(empty($info)){
            return;
        }else{
            $data = empty($info->apikey) ? [] : $info->apikey;
            return json_decode($data,true);
        }
    }

   /**
     * 修改配置或新增
     */
    public static function edit($name,array $apikey = []){
        $apikey = json_encode($apikey);
        $info = self::where(['name' => $name])->find();
        if(empty($info)){
            $data['name']   = trim($name);
            $data['apikey'] = $apikey;
            return self::insert($data);
        }else{
            $info->apikey = $apikey;
            return $info->save();
        }
    }
}