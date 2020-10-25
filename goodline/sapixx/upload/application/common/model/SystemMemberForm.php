<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 站点配置
 */

namespace app\common\model;
use think\Model;

class SystemMemberForm extends Model
{

    protected $pk = 'id';

    /**
     * @param int $miniapp_id
     * @param $uid
     */
    public static function getForm(int $miniapp_id,$uid){
        return self::where(['member_miniapp_id' => $miniapp_id,'uid' => $uid,'is_del' => 0])->whereTime('create_time','-7 day')->find();
    }

    /**
     * 增加FormID
     * @param integer $miniapp_id
     * @param integer $uid
     * @param string  $form_id
     * @return void
     */
    public static function addForm(int $miniapp_id,int $uid,$form_id){
        $data['member_miniapp_id'] = $miniapp_id;
        $data['form_id']           = $form_id;
        $data['uid']               = $uid;
        $data['is_del']            = 0;
        $data['create_time']       = time();
        return self::insert($data);
    }
}