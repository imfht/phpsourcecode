<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 *  会员充值记录
 */
namespace app\common\model;
use think\Model;

class SystemMemberBankRecharge extends Model{

    protected $pk = 'id';

    /**
     * 创建充值记录
     * @param [type] $data
     * @return void
     */
    public static function order($data){
        $memberBankRecharge = [
            'member_id'   => $data['member_id'],
            'create_time' => time(),
            'order_sn'    => $data['order_sn'],
            'money'       => $data['money'],
            'state'       => 0,
        ];
        return self::insert($memberBankRecharge);
    }

}