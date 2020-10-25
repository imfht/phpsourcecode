<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 权限管理
 */
namespace app\fastshop\model;
use think\Model;

class Auth extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_auth';

    //添加或编辑
    public static function edit($param){
        $data['types'] = trim($param['types']);
        $info = self::where(['member_miniapp_id' => $param['member_miniapp_id'],'member_id' => $param['id']])->find();
        if(empty($info)){
            $data['member_id']         = $param['id'];
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::insert($data);
        }else{
            return self::where(['id' => $info->id])->update($data);
        }
    } 

    /**
     * 是否有权限
     * @param int $uid
     * @return void
     */
    public static function getAuth(int $uid,int $types){
        $auth = self::where(['member_id' => $uid])->field('types')->find();
        if(empty($auth)){
            return true;
        }
        if ($auth->types > 0) {
            return $auth->types == $types ? true : false;
        }
        return true;
    }
}