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
use filter\Filter;

class SystemMemberSms extends Model
{

    protected $pk = 'id';

    /**
     * 增加
     * @param integer $miniapp_id
     * @param string  $sms
     * @return void
     */
    public static function Sms(int $miniapp_id,$sms,$url = ''){
        $data['member_miniapp_id'] = $miniapp_id;
        $data['message']            = trim(Filter::filter_escape($sms));
        $data['url']                = trim(Filter::filter_escape($url));
        $data['create_time']        = time();
        $data['is_new']             = 0;
        $data['is_read']            = 0;
        return self::insert($data);
    }
}