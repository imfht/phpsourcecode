<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 统计直接人数 Table<ai_fastshop_regnum>
 */
namespace app\fastshop\model;
use think\Model;
use app\common\model\SystemUserLevel;

class RegNum extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_regnum';
    protected $createTime = false;

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    } 

    /**
     * 统计人数
     */
    public static function countMum(int $appid,int $uid){
        $people_num = SystemUserLevel::where(['parent_id' => $uid,'level' => 1])->count();
        $allnum     = SystemUserLevel::where(['parent_id' => $uid])->count();
        $info = self::where(['uid' => $uid])->find();
        if(empty($info)){
            $data['member_miniapp_id'] = $appid;
            $data['uid']               = $uid;
            $data['num']               = $people_num;
            $data['allnum']            = $allnum;
            return self::insert($data);
        }else{
            $info->num    = $people_num;
            $info->allnum = $allnum;
            return $info->save();
        }
    }
}